<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Services\InstagramParser;


class InstagramParsePosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:parse-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse account\'s posts that were not updated more than the set period';

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
        $accounts = Account::where(function($q) {
            $q->whereDate('posts_data_captured_at', '<=', Carbon::now()->subMinutes(config('instagram.parser.period'))->toDateTimeString())
                ->orWhereNull('posts_data_captured_at');
        })->get();
        $instagramParser = new InstagramParser();
        foreach ($accounts as $account) {
            if($instagramParser->processAccountMedias($account)) {
                $account->posts_data_captured_at = Carbon::now()->toDateTimeString();
            }
            $account->save();
        }
    }
}
