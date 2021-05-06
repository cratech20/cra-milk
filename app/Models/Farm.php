<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;

    /**
     * Relations
     */

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
