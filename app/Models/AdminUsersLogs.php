<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUsersLogs extends Model
{
    use HasFactory;
    protected $table = "admin_users_logs";

    const TYPE_USERS_CREATE = 0;
    const TYPE_USERS_UPDATE = 1;
    const TYPE_USERS_DELETE = 2;
    const TYPE_USERS_LOGIN = 3;
    const TYPE_USERS_LOGOUT = 4;
    const TYPE_USERS_CHANGEPASSWORD = 5;
    const TYPE_USERS_CHANGESTATUS = 6;
    const TYPE_USERS_RESETPASSWORD = 7;
    const TYPE_USERS_CHANGEUSERNAME = 8;
    const TYPE_USERS_DELETUSER = 9;
    const TYPE_USERS_UPDATEROLE = 10;
    const TYPE_USERS_CREATEUSERFROMADMIN = 11;
    const TYPE_USERS_CREATEROLE = 12;
    const TYPE_USERS_DELETEROLE = 13;


    protected $fillable = [
        'user_id',
        'type',
        'meta',
    ];

    protected $appends = [
        'description', 'type_desc'
    ];

    public function getMetaAttribute($value)
    {
        return json_decode($value);
    }

    public function setMetaAttribute($value)
    {
        $this->attributes['meta'] = json_encode($value);
    }

    public function getTypeDescAttribute($value)
    {
        switch ($this->attributes['type']) {
            case 0:
                $result = 'Create';
                break;
            case 1:
                $result = 'Update';
                break;
            case 2:
                $result = 'Delete';
                break;
            case 3:
                $result = 'Login';
                break;
            case 4:
                $result = 'Logout';
                break;
            case 5:
                $result = 'ChangePassword';
                break;
            case 6:
                $result = 'ChangeStatus';
                break;
            case 7:
                $result = 'ResetPassword';
                break;
            case 8:
                $result = 'Change Username';
                break;
            case 9:
                $result = 'Delete User';
                break;
            case 10:
                $result = 'Update Role';
                break;
            case 11:
                $result = 'Create User by Admin';
                break;
            case 12:
                $result = 'Create Role by Admin';
                break;
            case 13:
                $result = 'Delete Role by Admin';
                break;
        }
        return $result;
    }

    public function getDescriptionAttribute()
    {
        switch ($this->attributes['type']) {
            case 0:
                $result = __('adminuUsersLogs.users.create', json_decode($this->attributes['meta'], true));
                break;
            case 1:
                $result = __('adminuUsersLogs.users.update', json_decode($this->attributes['meta'], true));
                break;
            case 2:
                $result = __('adminuUsersLogs.users.delete', json_decode($this->attributes['meta'], true));
                break;
            case 3:
                $result = __('adminuUsersLogs.users.login', json_decode($this->attributes['meta'], true));
                break;
            case 4:
                $result = __('adminuUsersLogs.users.logout', json_decode($this->attributes['meta'], true));
                break;
            case 5:
                $result = __('adminuUsersLogs.users.changepassword', json_decode($this->attributes['meta'], true));
                break;
            case 6:
                $result = __('adminuUsersLogs.users.changestatus', json_decode($this->attributes['meta'], true));
                break;
            case 7:
                $result = __('adminuUsersLogs.users.resetpassword', json_decode($this->attributes['meta'], true));
                break;
            case 8:
                $result = __('adminuUsersLogs.users.changename', json_decode($this->attributes['meta'], true));
                break;
            case 9:
                $result = __('adminuUsersLogs.users.deleteuser', json_decode($this->attributes['meta'], true));
                break;
            case 10:
                $result = __('adminuUsersLogs.users.roleuser', json_decode($this->attributes['meta'], true));
                break;
            case 11:
                $result = __('adminuUsersLogs.users.createuserviaadmin', json_decode($this->attributes['meta'], true));
                break;
            case 12:
                $result = __('adminuUsersLogs.users.createrolebyadmin', json_decode($this->attributes['meta'], true));
                break;
            case 13:
                $result = __('adminuUsersLogs.users.deleterolebyadmin', json_decode($this->attributes['meta'], true));
                break;
        }

        return $result;
    }
}
