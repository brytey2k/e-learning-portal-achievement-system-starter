<?php

namespace App\Console\Commands;

use App\Events\CommentWritten;
use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class WriteComment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:write-comment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Writes a comment for a user. THIS IS FOR DEBUGGING PURPOSES ONLY.';

    protected function configure(): void
    {
        $this->addOption('user', null, InputOption::VALUE_REQUIRED, 'User ID of commenter', 1);
        $this->addOption('comment', null, InputOption::VALUE_REQUIRED, 'Body of the comment', 'Comment body');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::find((int) $this->option('user'));

        if (!$user) {
            $this->error('User not found');
            return;
        }

        $comment = $user->comments()->create([
            'body' => $this->option('comment')
        ]);

        $this->info('Comment written');

        // fire event
        event(new CommentWritten($comment));
    }
}
