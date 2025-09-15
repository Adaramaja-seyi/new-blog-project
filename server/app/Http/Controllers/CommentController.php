<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    
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


    public function deleteComment($id)
    {
        $comment = Comment::where('user_id', Auth::id())->findOrFail($id);
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ], 200); // Use 200 for successful delete
    }

}
