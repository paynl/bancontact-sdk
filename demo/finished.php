<?php

require_once '../vendor/autoload.php';
$errors = [];

use Paynl\BancontactSDK\Gateway;

session_start();
if (!isset($_SESSION['apiToken']) || !isset($_SESSION['tokenCode'])) {
    header('location: index.php');
    die();
}
$gateway = new Gateway($_SESSION['tokenCode'], $_SESSION['apiToken']);


if (!isset($_REQUEST['orderId']) || !isset($_REQUEST['entranceCode'])) {
    header('location: new-transaction.php');
    die();
}

$bancontact = $gateway->bancontact($_REQUEST['orderId'], $_REQUEST['entranceCode']);

try {
    $status = $bancontact->getStatus();
    $info[] = "Current status: {$status->code} - {$status->description}";
} catch (Exception $e) {
    $errors[] = 'Error: ' . $e->getMessage();
}

try {
    $payerInfo = $bancontact->getPayerInfo();

    ?>
    <ul class="list-group">
        <li class="list-group-item">
            <strong>iban</strong><br/> <?= $payerInfo->iban ?>
        </li>
        <li class="list-group-item">
            <strong>bic</strong><br/> <?= $payerInfo->bic ?>
        </li>
        <li class="list-group-item">
            <strong>cardHolder</strong><br/> <?= $payerInfo->cardHolder ?>
        </li>
        <li class="list-group-item">
            <strong>customerId</strong><br/> <?= $payerInfo->customerId ?>
        </li>
        <li class="list-group-item">
            <strong>customerKey</strong><br/> <?= $payerInfo->customerKey ?>
        </li>
        <li class="list-group-item">
            <strong>expireDate</strong><br/> <?= $payerInfo->expireDate ?>
        </li>
        <li class="list-group-item">
            <strong>transactionRoutingMeans</strong><br/> <?= $payerInfo->transactionRoutingMeans ?>
        </li>
    </ul>
    <?php
} catch (Exception $e) {
    $errors[] = 'Error getting payerInfo: ' . $e->getMessage();
}

include './layout/layout.php';