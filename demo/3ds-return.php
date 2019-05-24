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

if (!isset($_POST['MD']) || !isset($_POST['PaRes'])) {
    $errors[] = 'POST does not contain expected data (PaRes, MD)';
} else {
    try {
        //capture the payment
        $result = $bancontact->authorize($_POST['MD'], $_POST['PaRes']);

        $nextUrl = 'finished.php?' . http_build_query(['orderId' => $_REQUEST['orderId'], 'entranceCode' => $_REQUEST['entranceCode']]);

        header('location: ' . $nextUrl);

    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}


include './layout/layout.php';