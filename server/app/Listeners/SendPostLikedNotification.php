<?php

namespace App\Listeners;

use App\Events\PostLiked;
use App\Notifications\PostLikedNotification;

class SendPostLikedNotification
{
    public function handle(PostLiked $event)
    {
        $event->post->user->notify(new PostLikedNotification(
            $event->likedBy,
            $event->likedAt,
            $event->post
        ));
    }
}