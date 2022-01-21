<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccountPost;
use App\Models\AccountStat;
use Illuminate\Support\Facades\DB;

class Account extends Model
{
    protected $appends = ['engagement_percentage', ''];
    protected $fillable = ['username', 'fullname', 'biography', 'profile_pic_filename', 'is_verified'];

    public function stats()
    {
        return $this->hasMany(AccountStat::class)->orderBy('created_at', 'DESC');
    }

    public function lastStat() {
        return $this->hasOne(AccountStat::class)->orderBy('created_at', 'DESC');
    }

    public function posts()
    {
        return $this->hasMany(AccountPost::class)->orderBy('created_at', 'DESC');
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
}
