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
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'category_id',
        'meta_title',
        'meta_description',
        'views_count',
        'published_at',
        'is_published',
        'user_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_published' => 'boolean',
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
        if (!$value) {
            return null;
        }

        // If it's already a full URL, return as is
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        // Convert relative path to full URL
        return url($value);
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
        return $this->belongsToMany(Tag::class);
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
