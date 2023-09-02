<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    public function getNextBadge(): ?Badge
    {
        return static::where('id', '>', $this->id)->first();
    }
}
