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
}
