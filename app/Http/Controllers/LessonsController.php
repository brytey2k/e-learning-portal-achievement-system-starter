<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonsController extends Controller
{

    public function view(Lesson $lesson)
    {
        $lesson->markAsWatchedBy(auth()->user());

        return response()->json([
            'message' => 'Lesson watched'
        ]);
    }

}
