<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPostStat extends Model
{
    public $timestamps = ["created_at"];

    protected $fillable = ['account_post_id', 'likes_count', 'comments_count'];

    const UPDATED_AT = null;

    public function post()
    {
        return $this->belongsTo(AccountPost::class);
    }
}
