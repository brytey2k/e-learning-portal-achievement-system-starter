<?php

namespace App\Models;

use App\Events\LessonWatched;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title'
    ];

    public function markAsWatchedBy(User $user): void
    {
        $user->watched()->syncWithoutDetaching([
            $this->id => [
                'watched' => true
            ]
        ]);

        event(new LessonWatched($this, $user));
    }

}
