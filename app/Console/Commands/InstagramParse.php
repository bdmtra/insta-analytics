<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;
use \App\Models\AccountPostStat;
use \App\Models\AccountPost;
use \App\Models\AccountStat;
use Carbon\Carbon;
use \InstagramScraper\Instagram;
use \GuzzleHttp\Client;
use Phpfastcache\Helper\Psr16Adapter;
use Illuminate\Support\Facades\Storage;

class InstagramParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse accounts that were not updated more than the set period';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $instagram = new Instagram(new Client(), config('instagram.parser.login'), config('instagram.parser.password'));
        $accounts = Account::whereDate('updated_at', '<=', Carbon::now()->subMinutes(config('instagram.parser.period'))->toDateTimeString())->get();
        foreach ($accounts as $account) {
            $accountResponse = $instagram->getAccount($account->username);

            $profilePicUrl = $accountResponse->getProfilePicUrl();
            $profilePicFilename = null;
            if($profilePicUrl) {
                $profilePicFilename = 'profile_pics/'.$account->username . '.jpg';
                Storage::disk('public')->put($profilePicFilename, file_get_contents($profilePicUrl));
            }
            $account->update([
                'fullname' => $accountResponse->getFullName(),
                'biography' => $accountResponse->getBiography(),
                'profile_pic_filename' => $profilePicFilename,
                'is_verified' => $accountResponse->isVerified(),
            ]);
            AccountStat::create([
                'account_id' => $account->id,
                'following_count' => $accountResponse->getFollowsCount(),
                'followers_count' => $accountResponse->getFollowedByCount(),
                'uploads_count' => $accountResponse->getMediaCount(),
            ]);

            while (!isset($mediasResponse) || $mediasResponse['hasNextPage']) {
                $mediasResponse = $instagram->getPaginateMedias($account->username, isset($mediasResponse) ? $mediasResponse['maxId'] : '');
                foreach ($mediasResponse['medias'] as $media) {
                    $post = AccountPost::firstOrCreate(['shortcode' => $media->getShortCode(), 'account_id' => $account->id]);
                    preg_match_all('/#\w+/iu', $media->getCaption(), $hashtags);
                    $post->update([
                        'type' => $media->getType(),
                        'link' => $media->getLink(),
                        'mentions' =>  array_column($media->getTaggedUsers(), 'username'),
                        'hashtags' => $hashtags[0],
                        'caption' => $media->getCaption(),
                        'external_created_at' => Carbon::parse($media->getCreatedTime())->toDateTimeString(),
                    ]);

                    AccountPostStat::create([
                        'account_post_id' => $post->id,
                        'likes_count' => $media->getLikesCount(),
                        'comments_count' => $media->getCommentsCount(),
                    ]);
                }
            }
        }
    }
}
