<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class UserLogin extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'is_approved','browser'];

    protected $appends = ['statusLabel'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getStatusLabelAttribute()
    {
        $label = null;
        if ($this->is_approved == 0) {
            $label = 'Pending';
        } else {
            $label = 'Approved';
        }

        return $label;
    }
}
