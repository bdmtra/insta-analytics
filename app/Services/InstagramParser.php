<?php


namespace App\Services;

use InstagramScraper\Instagram;
use App\Models\Proxy;
use \GuzzleHttp\Client;
use InstagramScraper\Exception\InstagramException;
use InstagramScraper\Exception\InstagramNotFoundException;
use InstagramScraper\Exception\InstagramAgeRestrictedException;
use App\Exceptions\InstagramParserNoProxiesException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Log;

class InstagramParser
{
    public $currentProxy = null;
    public $currentProxyErrorCount = null;
    public $scrapper = null;

    const MAX_PROXY_ERRORS = 5;

    const MAX_EXECUTION_PREVENT_TIME = 5;

    public function __construct()
    {
        $this->setNewScraper();
    }

    public function fetchAccount($username) {
        $accountResponse = null;
        while (!$accountResponse) {
            try {
                $accountResponse = $this->scrapper->getAccount($username);
                Log::channel('instagram-parser')->info('Got account`s info');
            } catch (InstagramException $exception) {
                $this->handleScrapperException($exception);
            } catch (RequestException $exception) {
                $this->handleScrapperException($exception);
            }  catch (ConnectException $exception) {
                $this->handleScrapperException($exception);
            } catch (InstagramParserNoProxiesException $exception) {
                return $exception;
            } catch (InstagramNotFoundException $exception) {
                Log::channel('instagram-parser')->info($exception->getMessage());
                return $exception;
            } catch (\Exception $exception) {
                Log::channel('instagram-parser')->info($exception->getMessage());
                return $exception;
            }
        }
        return $accountResponse;
    }

    public function fetchPaginateMedias($username, $maxId) {
        $mediasResponse = null;
        while (!$mediasResponse) {
            try {
                $mediasResponse = $this->scrapper->getPaginateMedias($username, $maxId);
                Log::channel('instagram-parser')->info('Got account`s posts');
            } catch (InstagramException $exception) {
                $this->handleScrapperException($exception);
            } catch (RequestException $exception) {
                $this->handleScrapperException($exception);
            }  catch (ConnectException $exception) {
                $this->handleScrapperException($exception);
            } catch (InstagramParserNoProxiesException $exception) {
                return $exception;
            } catch (InstagramNotFoundException $exception) {
                Log::channel('instagram-parser')->info($exception->getMessage());
                return $exception;
            } catch (\Exception $exception) {
                Log::channel('instagram-parser')->info($exception->getMessage());
                return $exception;
            }
        }
        return $mediasResponse;
    }

    public function processAccountMedias($account) {
        while (!isset($prevMediasResponse) || $prevMediasResponse['hasNextPage']) {
            $maxId = isset($prevMediasResponse) ? $prevMediasResponse['maxId'] : '';
            $mediasResponse = $this->fetchPaginateMedias($account->username, $maxId);
            if (is_array($mediasResponse)) {
                foreach ($mediasResponse['medias'] as $media) {
                    $account->saveAccountPost($media);
                }
            } else {
                return false;
            }
            $prevMediasResponse = $mediasResponse;
        }
        return true;
    }

    public function handleScrapperException($exception) {

        if (!app()->runningInConsole() && $requestTime = $_SERVER["REQUEST_TIME"]) {
            $currentExecutionTime  = time() - $requestTime;
            if (ini_get("max_execution_time") - $currentExecutionTime <= self::MAX_EXECUTION_PREVENT_TIME) {
                throw new \Exception();
            }
        }
        Log::channel('instagram-parser')->info($exception->getMessage());
        $this->currentProxyErrorCount++;
        if ($this->currentProxyErrorCount == self::MAX_PROXY_ERRORS) {
            $this->setNewScraper();
        } else {
            Log::channel('instagram-parser')->info('Retry with current proxy');
        }
    }

    public function setNewScraper() {
        $this->setNewProxy();
        $this->scrapper = new Instagram(new Client(['proxy' => $this->currentProxy->uri, 'verify' => false]));
    }

    public function setNewProxy() {
        $query = Proxy::orderBy('id','DESC');
        if ($this->currentProxy !== null) {
            $query->where('id', '<', $this->currentProxy->id);
        }
        $this->currentProxy = $query->first();
        $this->currentProxyErrorCount = 0;
        if(!$this->currentProxy) {
            throw new InstagramParserNoProxiesException();
        }
        Log::channel('instagram-parser')->info('Switch to proxy '.$this->currentProxy->uri);

        return $this->currentProxy;
    }
}
