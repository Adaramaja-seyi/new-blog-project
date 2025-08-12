<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Like a post
     */
    public function like(Request $request)
    {
        try {
            $request->validate([
                'post_id' => 'required|exists:posts,id'
            ]);

            $post = Post::findOrFail($request->post_id);
            $userId = Auth::id();

            // Check if already liked
            $existingLike = Like::where('post_id', $post->id)
                ->where('user_id', $userId)
                ->first();

            if ($existingLike) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post already liked'
                ], 422);
            }

            $like = Like::create([
                'post_id' => $post->id,
                'user_id' => $userId
            ]);

            return response()->json([
                'success' => true,
                'data' => $like,
                'message' => 'Post liked successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error liking post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unlike a post
     */
    public function unlike(Request $request)
    {
        try {
            $request->validate([
                'post_id' => 'required|exists:posts,id'
            ]);

            $post = Post::findOrFail($request->post_id);
            $userId = Auth::id();

            $like = Like::where('post_id', $post->id)
                ->where('user_id', $userId)
                ->first();

            if (!$like) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not liked yet'
                ], 422);
            }

            $like->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post unliked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error unliking post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get likes for a post
     */
    public function getPostLikes($postId)
    {
        try {
            $post = Post::findOrFail($postId);
            $likes = $post->likes()->with('user')->get();

            return response()->json([
                'success' => true,
                'data' => $likes,
                'count' => $likes->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching likes: ' . $e->getMessage()
            ], 500);
        }
    }
}
