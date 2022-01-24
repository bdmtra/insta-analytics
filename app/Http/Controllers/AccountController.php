<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\InstagramParser;
use Illuminate\Http\Request;
use Validator;
use Input;
use Redirect;
use InstagramScraper\Exception\InstagramNotFoundException;
use App\Exceptions\InstagramParserNoProxiesException;
use Illuminate\Support\Carbon;

class AccountController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $username =  Input::get('username');
        $account = Account::firstWhere('username', $username);
        if ($account) {
            return Redirect::to('/account/show/'.$username);
        }

        $validator = Validator::make(Input::all(), [
            'username' => 'required|max:30',
        ]);
        if ($validator->failed()) {
            return Redirect::to('/')->withErrors($validator)->withInput(Input::all());
        } else {
            $instagramParser = new InstagramParser();
            try {
                $accountResponse = $instagramParser->fetchAccount($username);
            } catch (InstagramNotFoundException $exception) {
                $validator->errors()->add('username', 'Such Instagram account doesn\'t exist');
                return Redirect::to('/')->withErrors($validator)->withInput(Input::all());
            } catch (InstagramParserNoProxiesException $exception) {
                $validator->errors()->add('username', 'Can\'t process this request now. Please try again later');
                return Redirect::to('/')->withErrors($validator)->withInput(Input::all());
            }
            $account = Account::create([
                'username' => $username
            ]);
            $account->saveAccountStat($accountResponse);
            $account->addDummyAccountStat();
            foreach ($accountResponse['medias'] as $media) {
                $account->saveAccountPost($media);
            }
            $account->update([
                'data_captured_at' => Carbon::now()->toDateTimeString()
            ]);

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

        $defaultChartSizes = ['width' => 1116,'height' => 250];
        $defaultChartOptionsRaw = function ($legend) { return '{tooltips:{mode:"index",intersect:!1,callbacks:{label:(e,a)=>{let t=a.datasets[e.datasetIndex].data[e.index];return number_format(t,0,".",",")+" "+a.datasets[e.datasetIndex].label}}},legend:{display:'.$legend.'},responsive:!0,maintainAspectRatio:!1,scales:{yAxes:[{gridLines:{display:!1},ticks:{userCallback:(e,a,t)=>{if(Math.floor(e)===e)return number_format(e,0,".",",")}}}],xAxes:[{gridLines:{display:!1}}]}}';};
        $lineFollowersChart = app()->chartjs
            ->name('lineFollowersChart')
            ->type('line')
            ->size($defaultChartSizes)
            ->labels(array_reverse($account->stats()->limit(15)->get()->pluck('data_capture_date')->toArray()))
            ->datasets([
                [
                    "label" => "Followers",
                    'data' => array_reverse($account->stats()->limit(15)->get()->pluck('followers_count')->toArray()),
                    'borderColor' => '#2BE39B',
                    'backgroundColor' => ['rgba(43, 227, 155, 0.6)', 'rgba(43, 227, 155, 0.05)'],
                    'fill' => true
                ],
            ])->optionsRaw($defaultChartOptionsRaw('false'));

        $lineFollowingChart = app()->chartjs
            ->name('lineFollowingChart')
            ->type('line')
            ->size($defaultChartSizes)
            ->labels(array_reverse($account->stats()->limit(15)->get()->pluck('data_capture_date')->toArray()))
            ->datasets([
                [
                    "label" => "Following",
                    'data' => array_reverse($account->stats()->limit(15)->get()->pluck('following_count')->toArray()),
                    'borderColor' => '#3ec1ff',
                    'backgroundColor' => ['rgba(62, 193, 255, 0.6)', 'rgba(62, 193, 255, 0.05)'],
                    'fill' => true
                ],
            ])->optionsRaw($defaultChartOptionsRaw('false'));
        $lineEngagementChart = app()->chartjs
            ->name('lineEngagementChart')
            ->type('line')
            ->size($defaultChartSizes)
            ->labels(array_reverse($account->stats()->limit(15)->get()->pluck('data_capture_date')->toArray()))
            ->datasets([
                [
                    "label" => "Engagement",
                    'data' => array_reverse($account->stats()->limit(15)->get()->pluck('engagement')->toArray()),
                    'borderColor' => '#ED4956',
                    'backgroundColor' => ['rgba(237, 73, 86, 0.4)', 'rgba(237, 73, 86, 0.05)'],
                    'fill' => true
                ],
            ])->optionsRaw($defaultChartOptionsRaw('false'));

        $mediaStatLineChart = app()->chartjs
            ->name('mediaStatLineChart')
            ->size($defaultChartSizes)
            ->type('line')
            ->labels($account->postDates(10))
            ->datasets([
                [
                    "label" => "Likes",
                    'data' => array_values($account->postAttributeCountByDates(10, 'likes_count')),
                    'borderColor' => '#ED4956',
                    'backgroundColor' => 'transparent',
                    'fill' => true
                ],
                [
                    "label" => "Comments",
                    'data' => array_values($account->postAttributeCountByDates(10, 'comments_count')),
                    'borderColor' => '#3ec1ff',
                    'backgroundColor' => 'transparent',
                    'fill' => true
                ],
                [
                    "label" => "Caption Word Count",
                    'data' => array_values($account->postAttributeCountByDates(10, 'caption_word_count')),
                    'borderColor' => '#2BE39B',
                    'backgroundColor' => 'transparent',
                    'fill' => true
                ],
            ])->optionsRaw($defaultChartOptionsRaw('true'));

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
