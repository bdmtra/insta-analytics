<?php


namespace App\Services;

use InstagramScraper\Instagram;
use App\Models\Proxy;
use \GuzzleHttp\Client;
use InstagramScraper\Exception\InstagramException;
use App\Exceptions\InstagramParserNoProxiesException;
use GuzzleHttp\Exception\RequestException;

class InstagramParser
{
    public $currentProxy = null;
    public $scrapper = null;

    public function __construct()
    {
        $this->setNewScraper();
    }

    public function fetchAccount($username) {
        $accountResponse = null;
        while (!$accountResponse) {
            try {
                $accountResponse = $this->scrapper->getAccount($username);
            } catch (InstagramException $exception) {
                $this->setNewScraper();
            } catch (RequestException $exception) {
                $this->setNewScraper();
            } catch (InstagramParserNoProxiesException $exception) {
                return false;
            }
        }
        return $accountResponse;
    }

    public function fetchPaginateMedias($username, $maxId) {
        $mediasResponse = null;
        while (!$mediasResponse) {
            try {
                $mediasResponse = $this->scrapper->getPaginateMedias($username, $maxId);
            } catch (InstagramException $exception) {
                $this->setNewScraper();

            } catch (RequestException $exception) {
                $this->setNewScraper();
            } catch (InstagramParserNoProxiesException $exception) {
                return false;
            }
        }
        return $mediasResponse;
    }

    public function processAccountMedias($account) {
        while (!isset($prevMediasResponse) || $prevMediasResponse['hasNextPage']) {
            $maxId = isset($prevMediasResponse) ? $prevMediasResponse['maxId'] : '';
            $mediasResponse = $this->fetchPaginateMedias($account->username, $maxId);
            if ($mediasResponse) {
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

    public function setNewScraper() {
        $this->setNewProxy();
        $this->scrapper = new Instagram(new Client(['proxy' => $this->currentProxy->uri]));
    }

    public function setNewProxy() {
        $query = Proxy::orderBy('id');
        if ($this->currentProxy !== null) {
            $query->where('id', '>', $this->currentProxy->id);
        }
        $this->currentProxy = $query->first();
        if(!$this->currentProxy) {
            throw new InstagramParserNoProxiesException();
        }

        return $this->currentProxy;
    }
}
