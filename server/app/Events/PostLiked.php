<?php

namespace App\Events;

use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostLiked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $likedBy;
    public $likedAt;

    public function __construct(Post $post, User $likedBy, $likedAt)
    {
        $this->post = $post;
        $this->likedBy = $likedBy;
        $this->likedAt = $likedAt;
    }
}
