<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roles_Users extends Model
{
    use HasFactory;
    protected $table = "role_user";
    protected $fillable = [
        'user_id','role_id'
    ];
}
