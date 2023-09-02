<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class CommentWritten
{
    use Dispatchable, SerializesModels;

    public Comment $comment;
    public User $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Comment $comment, User $user)
    {
        $this->comment = $comment;
        $this->user = $user;
    }
}
