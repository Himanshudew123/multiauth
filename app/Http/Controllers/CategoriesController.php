<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'Categories';
        // 1. Validate inputs: name (min 3, letters/spaces), dates
        $validated = $request->validate([
            'name'        => ['nullable','string','min:3','regex:/^[A-Za-z\s]+$/'],
            'start_date'  => ['nullable','date'],
            'end_date'    => ['nullable','date'],
        ], [
            'name.regex'  => 'The name may only contain letters and spaces.',
        ]);

        // 2. Build the query
        $query = Category::query();

        // 3. Apply name filter
        if (!empty($validated['name'])) {
            $query->where('name', 'like', '%' . $validated['name'] . '%');
        }

        // 4. Apply date range filter
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

        // 5. Paginate and return
        $categories = $query->latest()->paginate(5);
        return view('admin.categories.index', compact('categories','pageTitle'));
    }


    public function create()
    {
        $pageTitle ='Category | Create';
        return view('admin.categories.create' , compact('pageTitle'));
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
        $pageTitle ='Category | Update';
        $category = Category::where('uuid', $uuid)->firstOrFail();
        return view('admin.categories.edit', compact('category','pageTitle'));
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
        $category->softDelete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
    public function exportCSV()
    {
        $fileName = 'categories_' . now()->format('Ymd_His') . '.csv';

        $categories = Category::all(); // Get all customer records

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Name','Created At'];

        $callback = function () use ($categories, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

           

            foreach ($categories as $category) {
                fputcsv($file, [
                    $category->name,                   
                    $category->created_at->format('d-m-Y'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function exportPdfView(Request $request)
    {
        $categories = Category::all(); // Get all customer records
        return view('admin.categories.pdf', compact('categories'));
    }
}
