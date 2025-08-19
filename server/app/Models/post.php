<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'category_id',
        'tag_id',
        'featured_image',
        'status',
        'meta_title',
        'is_published',
        'slug',
        'user_id',
        'published_at',
        'views_count'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
        'views_count' => 'integer',
    ];

    /**
     * Get the featured image URL.
     *
     * @param  string  $value
     * @return string|null
     */
    public function getFeaturedImageAttribute($value)
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http') && str_contains($value, 'localhost')) {
            $parsedUrl = parse_url($value);
            return $parsedUrl['path'] ?? $value;
        }
        return str_starts_with($value, '/storage/') ? $value : '/storage/' . $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }
    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->where('status', 'approved');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Scope for published posts
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    // Scope for draft posts
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Increment views count
    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
