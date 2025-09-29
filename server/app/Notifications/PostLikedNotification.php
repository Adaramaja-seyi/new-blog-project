<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use App\Models\Post;

class PostLikedNotification extends Notification
{
    use Queueable;

    protected $likedBy;
    protected $likedAt;
    protected $post;

    public function __construct(User $likedBy, $likedAt, Post $post)
    {
        $this->likedBy = $likedBy;
        $this->likedAt = $likedAt;
        $this->post = $post;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your post was liked!')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->likedBy->name . ' liked your post on ' . date('F j, Y, g:i a', strtotime($this->likedAt)) . '.')
            ->line('Post Title: ' . $this->post->title)
            ->action('View Post', url('/posts/' . $this->post->id))
            ->line('Thank you for using our platform!');
    }
}
