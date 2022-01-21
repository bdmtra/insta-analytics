<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountPost;
use Illuminate\Http\Request;
use Validator;
use Input;
use Redirect;

use \InstagramScraper\Instagram;
use \GuzzleHttp\Client;
use Phpfastcache\Helper\Psr16Adapter;

class AccountController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(Input::all(), [
            'username' => 'required|max:30',
        ]);
        if ($validator->fails()) {
            return Redirect::to('/')
                ->withErrors($validator)
                ->withInput(Input::all());
        } else {
            $username =  Input::get('username');
            $account = Account::firstOrNew([
                'username' => $username
            ]);
            $account->save();

            return Redirect::to('/account/show/'.$username);
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        $account = Account::where('username', $username)->firstOrFail();

        $lineFollowersChart = app()->chartjs
            ->name('lineFollowersChart')
            ->type('line')
            ->labels(array_reverse($account->stats()->limit(15)->get()->pluck('data_capture_date')->toArray()))
            ->datasets([
                [
                    "label" => "Followers",
                    'data' => array_reverse($account->stats()->limit(15)->get()->pluck('followers_count')->toArray()),
                ],
            ]);

        $lineFollowingChart = app()->chartjs
            ->name('lineFollowingChart')
            ->type('line')
            ->labels(array_reverse($account->stats()->limit(15)->get()->pluck('data_capture_date')->toArray()))
            ->datasets([
                [
                    "label" => "Following",
                    'data' => array_reverse($account->stats()->limit(15)->get()->pluck('following_count')->toArray()),
                ],
            ]);
        $lineEngagementChart = app()->chartjs
            ->name('lineEngagementChart')
            ->type('line')
            ->labels(array_reverse($account->stats()->limit(15)->get()->pluck('data_capture_date')->toArray()))
            ->datasets([
                [
                    "label" => "Engagement",
                    'data' => array_reverse($account->stats()->limit(15)->get()->pluck('engagement')->toArray()),
                ],
            ]);

        $mediaStatLineChart = app()->chartjs
            ->name('mediaStatLineChart')
            ->type('line')
            ->labels($account->postDates(10))
            ->datasets([
                [
                    "label" => "Likes",
                    'data' => $account->postAttributeCountByDates(10, 'likes_count'),
                ],
                [
                    "label" => "Comments",
                    'data' => $account->postAttributeCountByDates(10, 'comments_count'),
                ],
                [
                    "label" => "Caption Word Count",
                    'data' => $account->postAttributeCountByDates(10, 'caption_word_count'),
                ],
            ]);

        $summaryStats = $account->stats()->limit(15)->get();
        $projectionTableData = [
            // label => number of days
            '30 days' => 30,
            '60 days' => 60,
            '3 months' => 90,
            '6 months' => 180,
            '9 months' => 270,
            '1 year' => 365,
            '1 year and half' => 549,
            '2 years' => 730,
        ];

        $latestPosts = $account->posts()->limit(10)->get()->toArray();
        $mentions = [];
        $hashtags = [];
        foreach ($latestPosts as $post) {
            foreach ($post['mentions'] as $mention) {
                if (isset($mentions[$mention])) {
                    $mentions[$mention]++;
                } else {
                    $mentions[$mention] = 1;
                }
            }
        }
        arsort($mentions);

        foreach ($latestPosts as $post) {
            foreach ($post['hashtags'] as $hashtag) {
                if (isset($hashtags[$hashtag])) {
                    $hashtags[$hashtag]++;
                } else {
                    $hashtags[$hashtag] = 1;
                }
            }
        }
        arsort($hashtags);

        return view('account/show', compact('account', 'lineFollowersChart', 'lineFollowingChart', 'lineEngagementChart', 'mediaStatLineChart', 'summaryStats', 'projectionTableData', 'latestPosts', 'hashtags', 'mentions'));
    }
}
?>
