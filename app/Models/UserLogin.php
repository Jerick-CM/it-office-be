<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class UserLogin extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'is_approved'];

    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }
}
