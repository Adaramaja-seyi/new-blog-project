<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getTags()
    {
        try {
            $tags = Tag::all();

            return response()->json([
                'success' => true,
                'data' => $tags
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tags',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search tags by query.
     */
    public function search(Request $request)
    {
        try {
            $query = $request->query('q', '');

            $tags = Tag::where('name', 'LIKE', "%{$query}%")
                ->withCount('posts')
                ->orderBy('name')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tags
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search tags',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function createTag(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:tags,name',
                'color' => 'nullable|string|max:7'
            ]);

            $validated['slug'] = Str::slug($validated['name']);

            $tag = Tag::create($validated);

            return response()->json([
                'success' => true,
                'data' => $tag,
                'message' => 'Tag created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        try {
            $tag->load(['posts' => function ($query) {
                $query->with('user')->latest();
            }]);

            return response()->json([
                'success' => true,
                'data' => $tag
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateTag(Request $request, Tag $tag)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', Rule::unique('tags')->ignore($tag->id)],
                'color' => 'nullable|string|max:7'
            ]);

            $validated['slug'] = Str::slug($validated['name']);

            $tag->update($validated);

            return response()->json([
                'success' => true,
                'data' => $tag,
                'message' => 'Tag updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteTag(Tag $tag)
    {
        try {
            $tag->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tag deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tag',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
