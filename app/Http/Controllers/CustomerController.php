<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{

    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('number')) {
            $query->where('number', 'like', '%' . $request->number . '%');
        }

        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->created_at);
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

        // Validate required fields
        $requiredFields = ['name', 'email', 'password', 'number', 'gender', 'bio'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                return response()->json(['success' => false, 'message' => "Missing or empty field: $field"]);
            }
        }

        // Custom validation rules
        $errors = [];

        // Name: no numbers or special characters
        if (!preg_match("/^[a-zA-Z\s]+$/", $data['name'])) {
            $errors[] = 'Name should contain only letters and spaces.';
        }

        // Email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        // Password length
        if (strlen($data['password']) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        // Number: only digits
        if (!preg_match("/^\d+$/", $data['number'])) {
            $errors[] = 'Phone number must contain only digits.';
        }

        // Gender: must be Male or Female
        if (!in_array($data['gender'], ['Male', 'Female'])) {
            $errors[] = 'Gender must be Male or Female.';
        }

        if (!empty($errors)) {
            return response()->json(['success' => false, 'message' => $errors]);
        }

        // Prepare validated data
        $name = $data['name'];
        $email = $data['email'];
        $password = bcrypt($data['password']);
        $number = $data['number'];
        $gender = $data['gender'];
        $bio = $data['bio'];

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            if ($photo->getSize() > 1024 * 1024) {
                return response()->json(['success' => false, 'message' => 'Photo must be less than 1MB.']);
            }
            $photoPath = $photo->store('photos', 'public');
        }

        // Save customer
        Customer::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'number' => $number,
            'gender' => $gender,
            'bio' => $bio,
            'photo' => $photoPath,
        ]);

        return response()->json(['success' => true, 'message' => 'Customer created successfully!']);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Decryption or processing failed: ' . $e->getMessage()]);
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
    
            // Validation
            $validator = Validator::make($data, [
                'name'     => ['required', 'regex:/^[a-zA-Z\s]+$/'],
                'email'    => ['required', 'email'],
                'password' => ['nullable', 'min:6'],
                'number'   => ['required', 'digits_between:10,15'],
                'gender'   => ['required', 'in:Male,Female'],
                'bio'      => ['required', 'string'],
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'field_errors' => $validator->errors(), // return field-wise errors
                    'message' => 'Validation failed.'
                ], 422);
            }
    
            $customer = Customer::where('uuid', $uuid)->first();
    
            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Customer not found.']);
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
