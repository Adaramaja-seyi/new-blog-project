<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::withCount('posts')
            ->orderBy('name')
            ->get();

        return response()->json($tags);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags',
            'color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/'
        ]);

        $tag = Tag::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'color' => $request->input('color', '#6c757d')
        ]);

        return response()->json($tag, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        return response()->json($tag->load('posts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->getKey(),
            'color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/'
        ]);

        $tag->update([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')),
            'color' => $request->input('color')
        ]);

        return response()->json($tag);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $tag->posts()->detach(); // Remove tag associations
        $tag->delete();

        return response()->json(['message' => 'Tag deleted successfully']);
    }

    /**
     * Search tags
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $tags = Tag::where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($tags);
    }
}
