<?php

namespace App\Console\Commands;

use App\Events\LessonWatched;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Console\Command;

class WatchLesson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:watch-lesson {--lesson=1: Lesson ID to watch} {--user=1: User ID of the watcher}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Watch lesson for a user. THIS IS FOR DEBUGGING PURPOSES ONLY.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = (int) $this->option('user');

        $lesson = Lesson::find((int) $this->option('lesson'));
        $user = User::find($userId);

        if (!$lesson) {
            $this->error('Lesson not found');
            return;
        }

        if (!$user) {
            $this->error('User not found');
            return;
        }

        $user->watched()->syncWithoutDetaching([
            $lesson->id => [
                'watched' => true
            ]
        ]);

        $this->info('Lesson watched');

        // fire event
        event(new LessonWatched($lesson, $user));
    }
}
