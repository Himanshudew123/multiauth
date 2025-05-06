<?php
namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

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
