<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\AccountPostStat;
use \App\Models\Account;

class AccountPost extends Model
{
    public $timestamps = ["created_at"];

    protected $appends = ['likes_count', 'comments_count', 'caption_word_count'];
    protected $fillable = ['account_id', 'shortcode', 'type', 'link', 'mentions', 'hashtags', 'caption'];
    protected $casts = [
        'mentions' => 'array',
        'hashtags' => 'array',
    ];

    const UPDATED_AT = null;

    public function stats() {
        return $this->hasMany(AccountPostStat::class)->orderBy('created_at', 'DESC');
    }

    public function lastStat() {
        return $this->hasOne(AccountPostStat::class)->orderBy('created_at', 'DESC');
    }

    public function getLikesCountAttribute() {
        return $this->lastStat ?  $this->lastStat->likes_count : null;
    }

    public function getCommentsCountAttribute() {
        return $this->lastStat ?  $this->lastStat->comments_count : null;
    }

    public function getCaptionWordCountAttribute() {
        return str_word_count($this->caption, 0);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
