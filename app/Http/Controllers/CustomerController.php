<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    public function index(Request $request)
{
    // Validate the inputs
    $validated = $request->validate([
        'name' => [
            'nullable',
            'string',
            'min:3',
            'regex:/^[A-Za-z\s]+$/'
        ],
        'number' => ['nullable', 'string'],
        'start_date' => ['nullable', 'date'],
        'end_date' => ['nullable', 'date'],
    ]);

    $query = Customer::query();

    // Name Filter (only if 3+ characters)
    if (!empty($validated['name'])) {
        $query->where('name', 'like', '%' . $validated['name'] . '%');
    }

    // Phone Number Filter (digits only)
    if (!empty($validated['number'])) {
        $number = preg_replace('/\D/', '', $validated['number']);
        if (!empty($number)) {
            $query->where('number', 'like', '%' . $number . '%');
        }
    }

    // Date Range Filter
    if (!empty($validated['start_date']) && !empty($validated['end_date'])) {
        $query->whereBetween('created_at', [$validated['start_date'], $validated['end_date']]);
    } elseif (!empty($validated['start_date'])) {
        $query->whereDate('created_at', '>=', $validated['start_date']);
    } elseif (!empty($validated['end_date'])) {
        $query->whereDate('created_at', '<=', $validated['end_date']);
    }

    $customers = $query->latest()->paginate(5);

    return view('admin.customers.index', compact('customers'));
}

    
    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        try {
            $encryptedPayload = decryptAES($request->input('payload'));
            $data = json_decode($encryptedPayload, true);

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Decryption failed: Invalid data.']);
            }
            $validator = Validator::make($data, [
                'name'     => ['required', 'regex:/^[a-zA-Z\s]+$/'],
                'email'    => ['required', 'email', 'unique:customers,email'],
                
                // Password validation: minimum 8 characters, at least one uppercase letter, 
                // at least one lowercase letter, at least one number, and one special character
                'password' => [
                    'required',
                    'min:8',
                    'regex:/[A-Z]/',           // At least one uppercase letter
                    'regex:/[a-z]/',           // At least one lowercase letter
                    'regex:/[0-9]/',           // At least one number
                    'regex:/[@$!%*?&]/',       // At least one special character
                ],
                
                // Phone number validation: must be between 10 and 15 digits, and cannot start with 0,1,2,3,4,5
                'number'   => ['required', 'digits_between:10,15', 'regex:/^[6-9]\d{9}$/'],  // Starts with 6-9 and then followed by digits
                
                // Gender validation
                'gender'   => ['required', 'in:Male,Female'],
                
                // Bio validation
                'bio'      => ['required', 'string'],
            ]);
            

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'field_errors' => $validator->errors(),
                    'message' => 'Validation failed.',
                ], 422);
            }

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');

                if ($photo->getSize() > 1024 * 1024) {
                    return response()->json(['success' => false, 'message' => 'Photo must be less than 1MB.']);
                }

                $photoPath = $photo->store('photos', 'public');
            }

            Customer::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => bcrypt($data['password']),
                'number'   => $data['number'],
                'gender'   => $data['gender'],
                'bio'      => $data['bio'],
                'photo'    => $photoPath,
            ]);

            return response()->json(['success' => true, 'message' => 'Customer created successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Processing failed: ' . $e->getMessage()]);
        }
    }

    public function show(string $uuid)
    {
        $customer = Customer::where('uuid', $uuid)->firstOrFail();
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(string $uuid)
    {
        $customer = Customer::where('uuid', $uuid)->firstOrFail();
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, string $uuid)
    {
        try {
            $encryptedPayload = decryptAES($request->input('payload'));
            $data = json_decode($encryptedPayload, true);

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Decryption failed: Invalid data.']);
            }

            $customer = Customer::where('uuid', $uuid)->firstOrFail();

            $validator = Validator::make($data, [
                'name'     => ['required', 'regex:/^[a-zA-Z\s]+$/'],
                'email'    => ['required', 'email', 'unique:customers,email,' . $customer->id],
                'password' => ['nullable', 'min:8'],
                'number'   => ['required', 'digits_between:10,15'],
                'gender'   => ['required', 'in:Male,Female'],
                'bio'      => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'field_errors' => $validator->errors(),
                    'message' => 'Validation failed.',
                ], 422);
            }

            $password = !empty($data['password']) ? bcrypt($data['password']) : $customer->password;

            $photoPath = $customer->photo;
            if ($request->hasFile('photo') || $request->input('remove_existing_photo') === '1') {
                if ($request->input('remove_existing_photo') === '1' && $photoPath) {
                    Storage::disk('public')->delete($photoPath);
                    $photoPath = null;
                }

                if ($request->hasFile('photo')) {
                    if ($request->file('photo')->getSize() > 1024 * 1024) {
                        return response()->json(['success' => false, 'message' => 'Photo must be less than 1MB.']);
                    }

                    $photoPath = $request->file('photo')->store('photos', 'public');
                }
            }

            $customer->update([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $password,
                'number'   => $data['number'],
                'gender'   => $data['gender'],
                'bio'      => $data['bio'],
                'photo'    => $photoPath,
            ]);

            return response()->json(['success' => true, 'message' => 'Customer updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $uuid)
    {
        $customer = Customer::where('uuid', $uuid)->firstOrFail();

        if ($customer->photo) {
            Storage::disk('public')->delete($customer->photo);
        }

        $customer->delete();

        return redirect()->back()->with('success', 'Customer deleted successfully!');
    }
    
    
}
