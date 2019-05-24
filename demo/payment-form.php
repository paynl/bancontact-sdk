<?php
require_once '../vendor/autoload.php';
$errors = [];
$info = [];

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

if (isset($_POST['cardHolder']) && isset($_POST['cardNumber']) && isset($_POST['expireYear']) && isset($_POST['expireMonth'])) {
    try {
        $authenticate = $bancontact->authenticate($_POST['cardHolder'], $_POST['cardNumber'], $_POST['expireYear'], $_POST['expireMonth']);

        $returnUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/3ds-return.php?' .
            http_build_query(['orderId' => $_REQUEST['orderId'], 'entranceCode' => $_REQUEST['entranceCode']]);

        // this function returns a self submitting form. After 3d secure the customer is redirected to the returnUrl
        echo $authenticate->get3DSForm($returnUrl);
        die();
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

ob_start();
?>
    <form method="post">
        <div class="form-group">
            <label for="cardHolder">Card Holder</label>
            <input type="text" class="form-control" name="cardHolder" id="cardHolder"
                   value="<?= @$_POST['cardHolder'] ?>"
                   placeholder="John Doe">
        </div>
        <div class="form-group">
            <label for="cardNumber">Card number</label>
            <input type="number" class="form-control" name="cardNumber" id="cardNumber"
                   value="<?= @$_POST['cardNumber'] ?>"
                   placeholder="1111 2222 3333 4444 5">
        </div>
        <div class="form-group">
            <label for="expireYear">Exp. year</label>
            <input type="text" class="form-control" name="expireYear" id="expireYear"
                   value="<?= @$_POST['expireYear'] ?>"
                   placeholder="2020">
        </div>
        <div class="form-group">
            <label for="expireMonth">Exp. month</label>
            <input type="text" class="form-control" name="expireMonth" id="expireMonth"
                   value="<?= @$_POST['expireMonth'] ?>"
                   placeholder="05">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
<?php

include './layout/layout.php';
