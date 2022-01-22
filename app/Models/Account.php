<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;


class Account extends Model
{
    public $timestamps = ["created_at"];

    protected $appends = ['engagement_percentage'];
    protected $fillable = ['username', 'fullname', 'biography', 'profile_pic_filename', 'is_verified', 'data_capture_status', 'data_captured_at', 'posts_data_capture_status', 'posts_data_captured_at'];

    const UPDATED_AT = null;

    const DATA_CAPTURE_STATUS_INACTIVE = 0;
    const DATA_CAPTURE_STATUS_ACTIVE = 1;

    public function stats()
    {
        return $this->hasMany(AccountStat::class)->orderBy('created_at', 'DESC');
    }

    public function lastStat() {
        return $this->hasOne(AccountStat::class)->orderBy('created_at', 'DESC');
    }

    public function posts()
    {
        return $this->hasMany(AccountPost::class)->orderBy('external_created_at', 'DESC');
    }

    public function averageStat($statName) {
        return $this->lastStat ? $this->lastStat->averageStat($statName) : null;
    }

    public function getEngagementPercentageAttribute() {
        return $this->lastStat ? $this->lastStat->engagement_percentage : null;
    }

    public function getDataCaptureDateAttribute() {
        return $this->lastStat ? $this->lastStat->data_capture_date : null;
    }

    public function getAverageStatPerDayChange($statName, $statsPickLimit = 15) {
        $stats = $this->stats()->limit($statsPickLimit)->get();
        return (int) floor($stats->sum($statName) / ($statsPickLimit - 1));
    }

    public function postDates($limit) {
        $result = AccountPost::select(DB::raw('DATE(external_created_at) as external_created_at_date'))->where('account_id', $this->id)->orderByDesc('external_created_at_date')->distinct()->limit($limit)->get();
        return $result->pluck('external_created_at_date')->all();
    }

    public function postAttributeCountByDates($limit, $attribute) {
        $dates = $this->postDates($limit);
        $result = [];
        foreach ($dates as $date) {
            $result[$date] = array_sum(array_column(AccountPost::where('account_id', $this->id)->whereDate('external_created_at', $date)->get()->toArray(), ($attribute)));
        }
        return $result;
    }

    public function saveAccountStat($accountResponse) {
        $profilePicUrl = $accountResponse->getProfilePicUrl();
        $profilePicFilename = null;
        if($profilePicUrl) {
            $profilePicFilename = 'profile_pics/'.$this->username . '.jpg';
            Storage::disk('public')->put($profilePicFilename, file_get_contents($profilePicUrl));
        }
        $this->update([
            'fullname' => $accountResponse->getFullName(),
            'biography' => $accountResponse->getBiography(),
            'profile_pic_filename' => $profilePicFilename,
            'is_verified' => $accountResponse->isVerified(),
        ]);
        AccountStat::create([
            'account_id' => $this->id,
            'following_count' => $accountResponse->getFollowsCount(),
            'followers_count' => $accountResponse->getFollowedByCount(),
            'uploads_count' => $accountResponse->getMediaCount(),
        ]);
    }

    public function saveAccountPost($mediaResponse) {
        $post = AccountPost::firstOrCreate(['shortcode' => $mediaResponse->getShortCode(), 'account_id' => $this->id]);
        preg_match_all('/#\w+/iu', $mediaResponse->getCaption(), $hashtags);
        $post->update([
            'type' => $mediaResponse->getType(),
            'link' => $mediaResponse->getLink(),
            'mentions' =>  array_column($mediaResponse->getTaggedUsers(), 'username'),
            'hashtags' => $hashtags[0],
            'caption' => $mediaResponse->getCaption(),
            'external_created_at' => Carbon::parse($mediaResponse->getCreatedTime())->toDateTimeString(),
        ]);

        AccountPostStat::create([
            'account_post_id' => $post->id,
            'likes_count' => $mediaResponse->getLikesCount(),
            'comments_count' => $mediaResponse->getCommentsCount(),
        ]);
    }
}
