<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(Request $request)
{
    // 1. Validate that 'name' is at least 3 characters if present
    $validated = $request->validate([
        'name' => [
            'nullable',
            'string',
            'min:3',
            'regex:/^[A-Za-z\s]+$/'
        ],
    ]);

    // 2. Build the query
    $query = Category::query();

    // 3. Apply the name filter only when it passes validation (i.e. 3+ chars)
    if (!empty($validated['name'])) {
        $query->where('name', 'like', '%' . $validated['name'] . '%');
    }

    // 4. Paginate and return
    $categories = $query->latest()->paginate(5);
    return view('admin.categories.index', compact('categories'));
}


    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
       
       
            // Decrypt the AES payload (ensure your decryptAES helper works securely)
            $encryptedPayload = $request->input('payload');
            $decryptedJson = decryptAES($encryptedPayload);
            
            
            $data = json_decode($decryptedJson, true);
           
    
    
            // Merge decrypted data into request for validation
            $request->merge($data);
    
            // Validate the decrypted data
            $request->validate([
                'name' => 'required|string|max:255'
            ]);
    
            // Store the category
            Category::create([
                'name' => $request->name
            ]);
    
            return response()->json(['success' => true, 'message' => 'Category Created successfully!']);
    }
    

    public function edit(string $uuid)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, string $uuid)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $category = Category::where('uuid', $uuid)->firstOrFail();
        $category->update(['name' => $request->name]);

        return response()->json(['success' => true, 'message' => 'Category updated successfully!']);
    }

    public function destroy(string $uuid)
    {
        $category = Category::where('uuid', $uuid)->firstOrFail();
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}
