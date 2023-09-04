<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WatchLessonCommandTest extends TestCase
{

    use RefreshDatabase;

    public function testWatchLessonCommandCanWatchALesson(): void
    {
        $this->seed();

        $output = $this->artisan('app:watch-lesson');

        $output->expectsOutput('Lesson watched');
        $output->assertExitCode(0);
    }

    public function testMissingUserWillGiveAnError(): void
    {
        $this->seed();

        $output = $this->artisan('app:watch-lesson', [
            '--user' => 9999953
        ]);

        $output->expectsOutput('User not found');
    }

    public function testMissingLessonWillGiveAnError(): void
    {
        $this->seed();

        $output = $this->artisan('app:watch-lesson', [
            '--lesson' => 9999953
        ]);

        $output->expectsOutput('Lesson not found');
    }
}
