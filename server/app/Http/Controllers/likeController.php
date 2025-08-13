<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Like a post
     */
    public function likePost(Post $post)
    {
        try {
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
    public function unlikePost(Post $post)
    {
        try {
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
     * Check if user has liked a post
     */
    public function checkLikeStatus(Post $post)
    {
        try {
            $userId = Auth::id();

            $isLiked = Like::where('post_id', $post->id)
                ->where('user_id', $userId)
                ->exists();

            return response()->json([
                'success' => true,
                'data' => [
                    'is_liked' => $isLiked,
                    'likes_count' => $post->likes()->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking like status: ' . $e->getMessage()
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

    /**
     * Like a comment
     */
    public function likeComment(Comment $comment)
    {
        try {
            $userId = Auth::id();

            // Check if already liked
            $existingLike = Like::where('comment_id', $comment->id)
                ->where('user_id', $userId)
                ->first();

            if ($existingLike) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment already liked'
                ], 422);
            }

            $like = Like::create([
                'comment_id' => $comment->id,
                'user_id' => $userId
            ]);

            return response()->json([
                'success' => true,
                'data' => $like,
                'message' => 'Comment liked successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error liking comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unlike a comment
     */
    public function unlikeComment(Comment $comment)
    {
        try {
            $userId = Auth::id();

            $like = Like::where('comment_id', $comment->id)
                ->where('user_id', $userId)
                ->first();

            if (!$like) {
                return response()->json([
                    'success' => false,
                    'message' => 'Comment not liked yet'
                ], 422);
            }

            $like->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment unliked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error unliking comment: ' . $e->getMessage()
            ], 500);
        }
    }
}
