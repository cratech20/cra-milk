<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cow extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getCalculatedNameAttribute()
    {
        return $this->name ?? 'Корова ID ' . $this->id . ' (' . $this->cow_id . ')';
    }

    public static function getNumberByCode($code)
    {
        try {
            return hexdec(strrev($code)) % 100000;
        } catch(\Exception $e) {
            return "";
        }
    }

    /**
     * Relations
     */

    public function group()
    {
        return $this->belongsTo(CowGroup::class);
    }
}
