<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function index($post)
    {
        $comments = Comment::with(['user'])
            ->where('post_id', $post)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'comments' => $comments
        ]);
    }

    public function store(Request $request, $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $post = Post::findOrFail($post);

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
            'status' => 'pending'  // Set default status
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

    public function update(Request $request, $id)
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

    public function destroy($id)
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
        $comment = Comment::findOrFail($id);

        // Check if user owns the post that this comment belongs to
        if ($comment->post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Comment approved successfully',
            'comment' => $comment->load(['user', 'post'])
        ]);
    }

    public function reject($id)
    {
        $comment = Comment::findOrFail($id);

        // Check if user owns the post that this comment belongs to
        if ($comment->post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->update(['status' => 'pending']);

        return response()->json([
            'message' => 'Comment rejected successfully',
            'comment' => $comment->load(['user', 'post'])
        ]);
    }

    public function markAsSpam($id)
    {
        $comment = Comment::findOrFail($id);

        // Check if user owns the post that this comment belongs to
        if ($comment->post->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->update(['status' => 'spam']);

        return response()->json([
            'message' => 'Comment marked as spam successfully',
            'comment' => $comment->load(['user', 'post'])
        ]);
    }
}
