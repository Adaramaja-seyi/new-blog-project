<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
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
            'status' => 'pending'
        ]);

        $comment->load('user');

        return response()->json([
            'comment' => $comment,
            'message' => 'Comment added successfully'
        ], 201);
    }

    public function show($id)
    {
        $comment = Comment::with(['user', 'replies.user'])->findOrFail($id);

        return response()->json([
            'comment' => $comment
        ]);
    }

    public function updateComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment = Comment::where('user_id', Auth::id())->findOrFail($id);
        $comment->update(['content' => $request->input('content')]);

        return response()->json([
            'comment' => $comment,
            'message' => 'Comment updated successfully'
        ], 200); // Use 200 for successful update
    }

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

    public function approve($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Check if user owns the post this comment belongs to
            if ($comment->post->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $comment->update(['status' => 'approved']);

            return response()->json([
                'success' => true,
                'message' => 'Comment approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a comment
     */
    public function reject($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Check if user owns the post this comment belongs to
            if ($comment->post->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $comment->update(['status' => 'pending']);

            return response()->json([
                'success' => true,
                'message' => 'Comment rejected successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark comment as spam
     */
    public function markAsSpam($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Check if user owns the post this comment belongs to
            if ($comment->post->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $comment->update(['status' => 'spam']);

            return response()->json([
                'success' => true,
                'message' => 'Comment marked as spam successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark comment as spam',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update comment status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:approved,pending,spam'
            ]);

            $comment = Comment::findOrFail($id);

            // Check if user owns the post this comment belongs to
            if ($comment->post->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $comment->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Comment status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comments for a specific post by slug
     */
    public function getPostComments($slug)
    {
        try {
            $post = Post::where('slug', $slug)->firstOrFail();
            // dd($post->id);
            $comments = Comment::with(['user'])
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
