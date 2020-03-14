<?php

namespace Xtnd\Cms;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Hash;  // Import Hash facade
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class CmsUser extends Authenticatable
{
    use Notifiable;

    protected $table = "cms_users";
    protected $guard = "cms_user";
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // public function setPasswordAttribute($password)
    // {
    //     $this->attributes['password'] = Hash::make($password);
    // }
}
