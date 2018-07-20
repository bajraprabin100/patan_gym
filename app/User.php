<?php

namespace App;

use Zizaco\Entrust\Traits\EntrustUserTrait;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

    public function userData()
    {
        return $this->belongsTo('App\Models\Admin\EmployeeParameter', 'id', 'user_id');
    }

    public static function userPermissionCan($permission)
    {
        $check_permission = UserPermission::where('user_id', '=', Auth::user()->id)
            ->join('permissions as p', 'p.id', '=', 'permission_user.permission_id')
            ->where('p.name', '=', $permission)
            ->first();
        if ($check_permission)
            return true;
        return false;
    }
//    public function language()
//    {
//        return $this->belongsTo('App\Models\Admin\EmployeeParameter');
//    }
}
