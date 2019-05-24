<?php


namespace Paynl\BancontactSDK;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Paynl\BancontactSDK\Api\Bancontact;
use Paynl\BancontactSDK\Api\Transaction;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\choose_handler;

class Gateway
{
    private $token;
    private $tokenCode;

    public function __construct($tokenCode, $token)
    {
        $this->tokenCode = $tokenCode;
        $this->token = $token;
    }

    public function transaction(): Transaction
    {
        $url = 'https://rest-api.pay.nl/v13/Transaction/';
        $client = $this->getHttpClient($url);

        return new Transaction($client);
    }

    private function getHttpClient(string $baseUrl): Client
    {
        $stack = new HandlerStack();
        $stack->setHandler(choose_handler());
        $stack->push($this->addHeaderMiddleware('Accept', 'application/json'));
        $authHeader = $this->getAuthHeader();
        if (!is_null($authHeader)) {
            $stack->push($this->addHeaderMiddleware('Authorization', $authHeader));
        }
        $client = new Client(['base_uri' => $baseUrl, 'handler' => $stack]);
        return $client;
    }

    private function addHeaderMiddleware($header, $content)
    {
        return Middleware::mapRequest(function (RequestInterface $r) use ($header, $content) {
            return $r->withHeader($header, $content);
        });
    }

    private function getAuthHeader()
    {
        if (!isset($this->token)) return null;
        $user = $this->tokenCode ?? 'token';
        $content = $user . ":" . $this->token;
        return "Basic " . base64_encode($content);
    }

    public function bancontact($orderId, $entranceCode): Bancontact
    {
        $url = 'https://secure-api.pay.nl/v1/Bancontact/';
        $client = $this->getHttpClient($url);

        return new Bancontact($client, $orderId, $entranceCode);
    }
}