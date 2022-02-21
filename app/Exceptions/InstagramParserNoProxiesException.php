<?php


namespace App\Exceptions;

use Illuminate\Support\Facades\Log;

class InstagramParserNoProxiesException extends \Exception
{
    public function __construct($message = "", $code = 404, $previous = null)
    {
        Log::channel('instagram-parser')->error('No more usable proxies available');
        parent::__construct($message, $code, $previous);
    }
}
