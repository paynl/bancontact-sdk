<?php


namespace Paynl\BancontactSDK\Api;

use Exception;
use GuzzleHttp\Client;

class Api
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $data
     * @throws Exception
     */
    protected function checkErrors(array $data)
    {
        if (isset($data['request']) &&
            isset($data['request']['result']) &&
            strtoupper($data['request']['result']) != 'TRUE' &&
            $data['request']['result'] != '1'
        ) {
            throw new Exception($data['request']['errorMessage']);
        }
    }
}