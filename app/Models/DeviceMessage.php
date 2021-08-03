<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceMessage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * yield to liters
     * @return float|int
     */
    public function getLitersAttribute()
    {
        return $this->yield / 1000;
    }
}
