<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function getAllPosts()
    {
        try {
            $userId = Auth::id();
            $filterUserId = request()->query('user_id');
            $search = request()->query('search');
            $categoryId = request()->query('category_id');
            $page  = (int) request()->query('page', 1);
            $limit = (int) request()->query('limit', 10);

            $query = Post::with(relations: ['user', 'comments.user', 'likes', 'category', 'tag'])->latest();

            if (!Auth::check()) {
                $query->where('status', 'published')
                    ->where('is_published', 'yes');
            }

            if ($filterUserId) {
                $query->where('user_id', $filterUserId);
            }
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('content', 'like', "%$search%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%$search%");
                        });
                });
            }

            $paginator = $query->paginate($limit, ['*'], 'page', $page);
            $posts = $paginator->getCollection()->map(function ($post) use ($userId) {
                return $this->mapPost($post);
            });

            return response()->json([
                'success' => true,
                'data' => $posts,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching posts: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function mapPost(Post $post)
    {

        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'content' => $post->content,
            'excerpt' => $post->excerpt,
            'featured_image' => $post->featured_image,
            'is_published' => $post->is_published,
            'status' => $post->status,
            'category_id' => $post->category_id,
            'tag_id' => $post->tag_id,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'views_count' => $post->views_count ?? 0,
            'user' => $post->user ? ['id' => $post->user->id, 'name' => $post->user->name] : null,
            'category' => $post->category ? ['id' => $post->category->id, 'name' => $post->category->name] : null,
            'tag' => $post->tag ? ['id' => $post->tag->id, 'name' => $post->tag->name] : null,
            'likes_count' => $post->likes->count(),
            'comments_count' => $post->comments->count(),
            'is_liked' => Auth::check() ? $post->likes->contains('user_id', Auth::id()) : false,


        ];
    }

    public function getPost(Post $post)
    {
        try {
            $post->incrementViews();
            $post = Post::with(['user', 'category', 'tag', 'comments.user', 'likes'])
                ->withCount(['comments', 'likes'])
                ->where('id', $post->id)->first();

            return response()->json([
                'success' => true,
                'data' => $post
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching post: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createPost(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'category_id' => 'nullable|integer|exists:categories,id',
                'tag_id' => 'nullable|integer|exists:tags,id',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'status' => 'nullable|string|in:draft,published',
                'meta_title' => 'nullable|string|max:255',
                'is_published' => 'nullable|string|in:yes,no',
            ]);

            if (isset($validated['tag_id']) && $validated['tag_id'] === '') {
                $validated['tag_id'] = null;
            }
            if (isset($validated['category_id']) && $validated['category_id'] === '') {
                $validated['category_id'] = null;
            }

            $imagePath = null;
            if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('posts', $imageName, 'public');
                $validated['featured_image'] = '/storage/' . $imagePath;
            }

            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $counter = 1;
            while (Post::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $postData = array_merge($validated, [
                'slug' => $slug,
                'user_id' => Auth::id(),
                'published_at' => $validated['status'] === 'published' ? now() : null
            ]);

            $post = Post::create($postData);
            $post->load(['user', 'category', 'tag', 'comments', 'likes']);

            return response()->json([
                'success' => true,
                'data' => $this->mapPost($post),
                'message' => 'Post created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating post: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePost(Request $request, Post $post)
    {
        // dd($request->all());
        try {

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'category_id' => 'nullable|integer|exists:categories,id',
                'tag_id' => 'nullable|integer|exists:tags,id',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'status' => 'required|string|in:draft,published',
                'meta_title' => 'nullable|string|max:255',
                'is_published' => 'required|string|in:yes,no',
            ]);

            $validated['category_id'] = $validated['category_id'] ?? null;
            $validated['tag_id'] = $validated['tag_id'] ?? null;

            if ($request->hasFile('featured_image')) {
                if ($post->featured_image) {
                    $oldImagePath = str_replace('/storage/', '', $post->featured_image);
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }
                }

                $image = $request->file('featured_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('posts', $imageName, 'public');
                $validated['featured_image'] = '/storage/' . $imagePath;
            }

            if (isset($validated['title']) && $validated['title'] !== $post->title) {
                $slug = Str::slug($validated['title']);
                $originalSlug = $slug;
                $counter = 1;
                while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter++;
                }
                $validated['slug'] = $slug;
            }
            $validated['published_at'] = $validated['status'] === 'published' ? now() : null;


            $post->update($validated);
            $post->load(['user', 'category', 'tag', 'comments', 'likes']);

            return response()->json([
                'success' => true,
                'data' => $this->mapPost($post),
                'message' => 'Post updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating post: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deletePost(Post $post)
    {
        try {
            if ($post->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this post'
                ], 403);
            }

            if ($post->featured_image) {
                $imagePath = str_replace('/storage/', '', $post->featured_image);
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting post: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('posts', $filename, 'public');
                $imageUrl = '/storage/' . $path;

                return response()->json([
                    'success' => true,
                    'data' => [
                        'url' => $imageUrl,
                        'path' => $path
                    ],
                    'message' => 'Image uploaded successfully'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file found'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPostsByStatus($status)
    {
        try {
            $userId = Auth::id();
            $query = Post::with(['user', 'comments.user', 'likes', 'category', 'tag'])
                ->where('status', $status)
                ->latest();

            $paginator = $query->paginate(10);
            $posts = $paginator->getCollection()->map(function ($post) use ($userId) {
                return $this->mapPost($post, $userId);
            });

            return response()->json([
                'success' => true,
                'data' => $posts,
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching posts: ' . $e->getMessage()
            ], 500);
        }
    }

}
