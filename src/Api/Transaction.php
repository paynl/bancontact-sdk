<?php


namespace Paynl\BancontactSDK\Api;

use Paynl\BancontactSDK\Result\Transaction as TransactionResult;
use function GuzzleHttp\json_decode;

class Transaction extends Api
{
    /**
     * @param array $data
     * @return TransactionResult
     * @throws \Exception
     */
    public function start(array $data): TransactionResult
    {
        $response = $this->client->post('start/json/', [
            'json' => array_merge($data, ['finishUrl' => 'https://www.pay.nl', 'paymentOptionId' => 436])
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $this->checkErrors($data);

        return TransactionResult::fromArray($data);
    }
}