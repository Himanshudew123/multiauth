<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Carbon\Carbon;
;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Validate the inputs
        $validated = $request->validate([
            'name'       => [
                'nullable',
                'string',
                'min:3',
                'regex:/^[A-Za-z\s]+$/'
            ],
            'number'     => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date'],
        ], [
            'name.regex' => 'The name may only contain letters and spaces.',
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
            $query->whereBetween('created_at', [
                $validated['start_date'],
                $validated['end_date'] . ' 23:59:59'
            ]);
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
                'password' => [
                    'required',
                    'min:8',
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*?&]/',
                ],
                'number'   => ['required', 'digits_between:10,15', 'regex:/^[6-9]\d{9}$/'],
                'gender'   => ['required', 'in:1,2,3'],
                'bio'      => ['required', 'string'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success'      => false,
                    'field_errors' => $validator->errors(),
                    'message'      => 'Validation failed.',
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
        // Validate decrypted AES payload data
        $encryptedPayload = decryptAES($request->input('payload'));
        $data = json_decode($encryptedPayload, true);
        $request->merge($data);

        $validator = Validator::make($data, [
            'name'   => ['required', 'regex:/^[a-zA-Z\s]+$/'],
            'email'  => ['required', 'email', 'unique:customers,email,' . Customer::where('uuid', $uuid)->firstOrFail()->id],
            'password'=> ['nullable', 'min:8'],
            'number' => ['required', 'digits_between:10,15'],
            'gender' => ['required', 'in:1,2,3'],
            'bio'    => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'      => false,
                'field_errors' => $validator->errors(),
                'message'      => 'Validation failed.',
            ], 422);
        }

        $customer = Customer::where('uuid', $uuid)->firstOrFail();
        $password = !empty($data['password']) ? bcrypt($data['password']) : $customer->password;

        // Photo handling
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

        // Update record
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
    }

    public function destroy(string $uuid)
    {
        $customer = Customer::where('uuid', $uuid)->firstOrFail();
    
        if ($customer->photo) {
            Storage::disk('public')->delete($customer->photo);
        }
    
        $customer->softDelete(); // Use custom soft delete logic
        return redirect()->back()->with('success', 'Customer deleted successfully!');
    }

    public function exportCSV()
{
    $fileName = 'customers_' . now()->format('Ymd_His') . '.csv';

    $customers = Customer::all(); // Get all customer records

    $headers = [
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename={$fileName}",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $columns = ['Name', 'Email', 'Phone Number', 'Gender', 'Created At'];

    $callback = function () use ($customers, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        $genderMap = [1 => 'Male', 2 => 'Female', 3 => 'Other'];

        foreach ($customers as $customer) {
            fputcsv($file, [
                $customer->name,
                $customer->email,
                $customer->number,
                $genderMap[$customer->gender] ?? 'N/A',
                $customer->created_at->format('d-m-Y H:i:s'),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
 
}
