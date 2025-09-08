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

   
}
