<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Account;

class AccountStat extends Model
{
    public $timestamps = ['created_at'];

    protected $appends = ['data_capture_date', 'data_capture_short_weekday', 'following_count_change', 'followers_count_change', 'uploads_count_change', 'engagement', 'engagement_percentage'];
    protected $fillable = ['account_id', 'following_count', 'followers_count', 'uploads_count'];

    const UPDATED_AT = null;

    public function previous() {
        return AccountStat::where([['id', '<', $this->id], ['account_id', '=',  $this->account_id]])->orderBy('id','DESC')->first();
    }

    public function getDataCaptureDateAttribute() {
        return Carbon::parse($this->created_at)->format('Y-m-d');
    }

    public function getDataCaptureShortWeekdayAttribute() {
        return Carbon::parse($this->created_at)->format('D');
    }

    public function getCountAttributeChange($attribute) {
        $prevStat = $this->previous();
        return $prevStat ? ($this->$attribute - $prevStat->$attribute) : null;
    }

    public function getFollowingCountChangeAttribute() {
        return $this->getCountAttributeChange('following_count');
    }

    public function getFollowersCountChangeAttribute() {
        return $this->getCountAttributeChange('followers_count');
    }

    public function getUploadsCountChangeAttribute() {
        return $this->getCountAttributeChange('uploads_count');
    }

    public function averageStat($statName) {
        $query = $this->account->posts()->limit(10)->whereDate('created_at',  '<=', $this->created_at);
        $postsArray = $query->get()->toArray();
        $sum = array_sum(array_column($postsArray, $statName));
        $count = count($postsArray);
        return $count ? (int) floor($sum/$count) : 0;
    }

    public function getEngagementAttribute()
    {
        return ($this->averageStat('likes_count') / $this->followers_count);
    }

    public function getEngagementPercentageAttribute()
    {
        return $this->engagement * 100;
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
