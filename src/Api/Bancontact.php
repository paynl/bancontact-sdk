<?php


namespace Paynl\BancontactSDK\Api;

use GuzzleHttp\Client;
use Paynl\BancontactSDK\Result;
use function GuzzleHttp\json_decode;

class Bancontact extends Api
{
    private $orderId;
    private $entranceCode;

    public function __construct(Client $client, $orderId, $entranceCode)
    {
        parent::__construct($client);

        $this->orderId = $orderId;
        $this->entranceCode = $entranceCode;
    }

    /**
     * @return Result\GetStatus
     * @throws \Exception
     */
    public function getStatus(): Result\GetStatus
    {
        $response = $this->client->post('getStatus/json', [
            'form_params' => $this->getData()
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        $this->checkErrors($result);

        return Result\GetStatus::fromArray($result['transactionStatus']);

    }

    private function getData(): array
    {
        return [
            'orderId' => $this->orderId,
            'entranceCode' => $this->entranceCode,
        ];
    }

    /**
     * @param $cardHolder
     * @param $cardNumber
     * @param $expireYear
     * @param $expireMonth
     * @return Result\Authenticate
     * @throws \Exception
     */
    public function authenticate($cardHolder, $cardNumber, $expireYear, $expireMonth): Result\Authenticate
    {
        $data = array_merge([
            'cardHolder' => $cardHolder,
            'cardNumber' => $cardNumber,
            'expireYear' => $expireYear,
            'expireMonth' => $expireMonth
        ], $this->getData());

        $response = $this->client->post('authenticate/json/', [
            'form_params' => $data
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        $this->checkErrors($result);

        return Result\Authenticate::fromArray($result['authenticate']);
    }

    /**
     * @param string $MD
     * @param string $paRes
     * @return bool
     * @throws \Exception
     */
    public function authorize(string $MD, string $paRes): bool
    {
        $data = array_merge(['paRes' => $paRes,
            'MD' => $MD
        ], $this->getData());

        $response = $this->client->post('authorize/json', [
            'form_params' => $data
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        $this->checkErrors($result);

        return true;
    }

    /**
     * @return Result\GetPayerInfo
     * @throws \Exception
     */
    public function getPayerInfo(): Result\GetPayerInfo
    {
        $response = $this->client->post('getPayerInfo/json', [
            'form_params' => $this->getData()
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        $this->checkErrors($result);


        return Result\GetPayerInfo::fromArray($result['customer']);
    }
}