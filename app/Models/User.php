<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use MoveMoveIo\DaData\Enums\BranchType;
use MoveMoveIo\DaData\Enums\CompanyType;
use MoveMoveIo\DaData\Facades\DaDataCompany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // TODO вынести в сервайс
    public static function getDataByInn($inn)
    {
        $result = DaDataCompany::id($inn, 1, null, BranchType::MAIN, CompanyType::LEGAL);

        return $result['suggestions'][0] ?? null;
    }

    /**
     * Relations
     */

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function cows()
    {
        return $this->hasMany(Cow::class);
    }

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }

    public function farms()
    {
        return $this->hasMany(Farm::class);
    }

    public function cowGroups()
    {
        return $this->hasMany(CowGroup::class);
    }

    public function isBlock()
    {
        return $this->status === 0;
    }
}
