<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    // Fetch top-level comments for a post by slug
    public function index($postSlug)
    {
        $post = Post::where('slug', $postSlug)->firstOrFail();
        $comments = Comment::with(['user'])
            ->where('post_id', $post->id)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'comments' => $comments
        ]);
    }

    public function createComment(Request $request, $postSlug)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $post = Post::where('slug', $postSlug)->firstOrFail();

        // Optional: Check if parent_id belongs to the same post
        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if (!$parentComment || $parentComment->post_id != $post->id) {
                return response()->json(['message' => 'Invalid parent comment'], 422);
            }
        }

        $comment = Comment::create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'parent_id' => $request->input('parent_id'),
            // 'status' => 'pending'
        ]);

        $comment->load(relations: 'user');

        return response()->json([
            'comment' => $comment,
            'message' => 'Comment added successfully'
        ], 201);
    }

    

    // 

    public function deleteComment($id)
    {
        $comment = Comment::where('user_id', Auth::id())->findOrFail($id);
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ], 200); // Use 200 for successful delete
    }

    public function getRecentComments()
    {
        // Get the 10 most recent comments with user and post info
        $comments = Comment::with(['user', 'post'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'comments' => $comments
        ]);
    }

   

    
    //   Get comments for a specific post by slug
     
    public function getPostComments($slug)
    {
        try {
            $post = Post::where('slug', $slug)->firstOrFail();
            // dd($post->id);
            $comments = Comment::with('user')
                ->where('post_id', $post->id)
                // ->where('status', 'approved')
                ->whereNull('parent_id')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $comments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
