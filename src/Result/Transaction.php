<?php

namespace Paynl\BancontactSDK\Result;

use Exception;

/**
 * Class Transaction
 *
 * @property-read string $orderId
 * @property-read string $entranceCode
 * @property-read string $urlIntent
 * @property-read string $qrImage
 */
class Transaction extends Result
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public static function fromArray(array $data)
    {
        if (!isset($data['qrCode'])) {
            throw new Exception('Required data for bancontact is not present, please contact PAY. to enable this for your account');
        }

        $newData = [];

        $newData['orderId'] = $data['transaction']['transactionId'];
        $newData['entranceCode'] = $data['qrCode']['entranceCode'];
        $newData['urlIntent'] = $data['qrCode']['urlIntent'];
        $newData['qrImage'] = $data['qrCode']['image'];

        return parent::fromArray($newData);
    }
}