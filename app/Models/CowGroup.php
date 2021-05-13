<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CowGroup extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getCalculatedNameAttribute()
    {
        return $this->name ?? 'Группа ID ' . $this->id;
    }
}
