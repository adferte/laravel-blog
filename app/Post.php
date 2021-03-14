<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    protected $fillable = ['title', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUserPosts($query, $userPosts)
    {
        if ($userPosts) {
            $query = $query->where('user_id', Auth::id());
        }
        return $query;
    }
}
