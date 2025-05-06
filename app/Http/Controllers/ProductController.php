<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'tags']);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('price')) {
            $query->where('price', '<=', $request->price);
        }

        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        $products = $query->orderByDesc('created_at')->paginate(5);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.products.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        try {
            $decryptedPayload = decryptAES($request->input('payload'));
            $data = json_decode($decryptedPayload, true);

            if (!$data) {
                return response()->json(['success' => false, 'message' => 'Invalid data after decryption.']);
            }

            $requiredFields = ['name', 'price', 'category_id', 'tags'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                    return response()->json(['success' => false, 'message' => "Missing or empty field: $field"]);
                }
            }

            $errors = [];

            if (!preg_match("/^[\p{L}\s0-9\-.,]+$/u", $data['name'])) {
                $errors[] = 'Product name contains invalid characters.';
            }

            if (!is_numeric($data['price']) || $data['price'] < 0) {
                $errors[] = 'Price must be a non-negative number.';
            }

            if (!is_numeric($data['category_id'])) {
                $errors[] = 'Invalid category selected.';
            }

            if (!is_array($data['tags'])) {
                $errors[] = 'Tags must be an array.';
            }

            if (!empty($errors)) {
                return response()->json(['success' => false, 'message' => $errors]);
            }

            $imagePaths = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $path = $image->store('products', 'public');
                        $imagePaths[] = $path;
                    }
                }
            }

            $product = new Product();
            $product->uuid = Str::uuid();
            $product->name = $data['name'];
            $product->price = $data['price'];
            $product->category_id = $data['category_id'];
            $product->photo = json_encode($imagePaths);
            $product->save();

            $product->tags()->sync($data['tags']);

            return response()->json(['success' => true, 'message' => 'Product created successfully.']);

        } catch (\Exception $e) {
            Log::error('Product Store Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error.'], 500);
        }
    }

    public function show($uuid)
    {
        $product = Product::with(['category', 'tags'])->where('uuid', $uuid)->firstOrFail();
        return view('admin.products.show', compact('product'));
    }

    public function edit($uuid)
    {
        $product = Product::with('tags')->where('uuid', $uuid)->firstOrFail();
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.products.edit', compact('product', 'categories', 'tags'));
    }

    public function update(Request $request, $uuid)
    {
        try {
            $decrypted = decryptAES($request->input('payload'));
            $data = json_decode($decrypted, true);
           
            if (!$data) {
                return response()->json(['message' => 'Invalid decrypted data.'], 422);
            }
    
            $validator = Validator::make($data, [
                'name'        => ['required', 'string', 'max:255'],
                'price'       => ['required', 'numeric', 'min:0'],
                'category_id' => ['nullable', 'exists:categories,id'],
                'tags'        => ['nullable', 'array'],
                'tags.*'      => ['exists:tags,id']  // Ensure each tag ID exists in the tags table
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            $validated = $validator->validated();
    
            $product = Product::where('uuid', $uuid)->firstOrFail();
            $product->name = $validated['name'];
            $product->price = $validated['price'];
            $product->category_id = $validated['category_id'] ?? null;
    
            // Handle photo upload or removal
            if ($request->hasFile('photo')) {
                // Delete old photos if any
                if ($product->photo) {
                    foreach (json_decode($product->photo, true) ?? [] as $oldPhotoPath) {
                        Storage::disk('public')->delete($oldPhotoPath);
                    }
                }
    
                // Upload new photo
                $file = $request->file('photo');
                $path = $file->store('products', 'public');
                $product->photo = json_encode([$path]);
            } elseif ($request->input('remove_existing_photo') === '1') {
                // Remove existing photo if requested
                if ($product->photo) {
                    foreach (json_decode($product->photo, true) ?? [] as $photoPath) {
                        Storage::disk('public')->delete($photoPath);
                    }
                }
                $product->photo = null;
            }
    
            // Save product data
            $product->save();
    
            // Sync tags: If tags exist, sync them; otherwise, detach them
            if (isset($validated['tags']) && !empty($validated['tags'])) {
                $product->tags()->sync($validated['tags']);
            } else {
                $product->tags()->detach();
            }
    
            return response()->json(['message' => 'Product updated successfully.']);
    
        } catch (\Exception $e) {
            \Log::error('Product Update Error: ' . $e->getMessage());
            return response()->json(['message' => 'Server error.'], 500);
        }
    }
    
}
