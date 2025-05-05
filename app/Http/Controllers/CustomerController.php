<?php

namespace App\Http\Controllers;

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
        // dd($request);
        $encryptedPayload = decryptAES($request->input('payload'));

        try {
            // Decrypt the encrypted data

            $data = json_decode($encryptedPayload, true);



            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Decryption failed: Invalid data.']);
            }

            // Ensure all required fields are present
            $requiredFields = ['name', 'email', 'password', 'number', 'gender', 'bio'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return response()->json(['success' => false, 'message' => 'Missing field: ' . $field]);
                }
            }

            $name = $data['name'];
            $email = $data['email'];
            $password = bcrypt($data['password']);
            $number = $data['number'];
            $gender = $data['gender'];
            $bio = $data['bio'];

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('photos', 'public');
            }

            // Create the customer record
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
            return response()->json(['success' => false, 'message' => 'Decryption failed: ' . $e->getMessage()]);
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
        $encryptedPayload = decryptAES($request->input('payload'));
        
        try {
            // Decrypt the encrypted data
            $data = json_decode($encryptedPayload, true);
    
            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Decryption failed: Invalid data.']);
            }
    
            // Ensure all required fields are present
            $requiredFields = ['name', 'email', 'password', 'number', 'gender', 'bio'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return response()->json(['success' => false, 'message' => 'Missing field: ' . $field]);
                }
            }
    
            // Find the customer by UUID
            $customer = Customer::where('uuid', $uuid)->first();
    
            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Customer not found.']);
            }
    
            // Check if password is being updated and hash it
            $password = isset($data['password']) && !empty($data['password']) ? bcrypt($data['password']) : $customer->password;
    
            // Check if the photo is being updated, otherwise retain the existing photo
            $photoPath = $customer->photo;
            if ($request->hasFile('photo') || $request->input('remove_existing_photo') === '1') {
                // Remove the existing photo if marked
                if ($request->input('remove_existing_photo') === '1' && $photoPath) {
                    Storage::disk('public')->delete($photoPath);
                    $photoPath = null; // Clear the photo path
                }
    
                // If a new photo is uploaded
                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')->store('photos', 'public');
                }
            }
    
            // Update the customer record
            $customer->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $password,
                'number' => $data['number'],
                'gender' => $data['gender'],
                'bio' => $data['bio'],
                'photo' => $photoPath,
            ]);
    
            return response()->json(['success' => true, 'message' => 'Customer updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Decryption failed: ' . $e->getMessage()]);
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
