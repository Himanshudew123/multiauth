<?php
namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function index(Request $request)
    {
        // 1. Validate inputs
        $validated = $request->validate([
            'name'       => ['nullable','string','min:3','regex:/^[A-Za-z\s]+$/'],
            'start_date' => ['nullable','date'],
            'end_date'   => ['nullable','date'],
        ], [
            'name.regex' => 'The name may only contain letters and spaces.',
        ]);
    
        // 2. Build base query
        $query = Tag::query();
    
        // 3. Name filter
        if (!empty($validated['name'])) {
            $query->where('name', 'like', '%' . $validated['name'] . '%');
        }
    
        // 4. Date range filter
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
    
        // 5. Paginate
        $tags = $query->latest()->paginate(5);
    
        return view('admin.tags.index', compact('tags'));
    }
    

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        $encryptedPayload = $request->input('payload');
        $decryptedJson = decryptAES($encryptedPayload);
        $data = json_decode($decryptedJson, true);

        $request->merge($data);

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Tag::create(['name' => $request->name]);

        return response()->json(['success' => true, 'message' => 'Tag created successfully!']);
    }

    public function edit(string $uuid)
    {
        $tag = Tag::where('uuid', $uuid)->firstOrFail();
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, string $uuid)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $tag = Tag::where('uuid', $uuid)->firstOrFail();
        $tag->update(['name' => $request->name]);

        return response()->json(['success' => true, 'message' => 'Tag updated successfully!']);
    }

    public function destroy(string $uuid)
    {
        $tag = Tag::where('uuid', $uuid)->firstOrFail();
        $tag->delete();

        return redirect()->back()->with('success', 'Tag deleted successfully!');
    }
}
