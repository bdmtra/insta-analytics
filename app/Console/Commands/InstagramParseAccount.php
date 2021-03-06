<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Services\InstagramParser;
use InstagramScraper\Model\Account as InstagramScraperAccount;
use Illuminate\Support\Facades\Log;

class InstagramParseAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:parse-account';

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
        $accounts = Account::where(function($q) {
            $q->whereDate('data_captured_at', '<=', Carbon::now()->subMinutes(config('instagram.parser.period'))->toDateTimeString())
                ->orWhereNull('data_captured_at');
        })->get();
        $instagramParser = new InstagramParser();
        foreach ($accounts as $account) {
            Log::channel('instagram-parser')->info('Start to parse account '.$account->username);
            $accountResponse = $instagramParser->fetchAccount($account->username);
            if($accountResponse instanceof InstagramScraperAccount) {
                $account->saveAccountStat($accountResponse);
                $account->data_captured_at = Carbon::now()->toDateTimeString();
                $account->save();
            }
            Log::channel('instagram-parser')->info('Stop to parse account '.$account->username);
        }
    }
}
