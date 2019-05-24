<?php
require_once '../vendor/autoload.php';

use Paynl\BancontactSDK\Gateway;

$errors = [];

session_start();
if (!isset($_SESSION['apiToken']) || !isset($_SESSION['tokenCode'])) {
    header('location: index.php');
    die();
}
$gateway = new Gateway($_SESSION['tokenCode'], $_SESSION['apiToken']);

if (isset($_POST['serviceId']) && isset($_POST['amount'])) {
    $_SESSION['serviceId'] = $_POST['serviceId'];

    try {
        $transaction = $gateway->transaction()->start([
            'serviceId' => $_POST['serviceId'],
            'amount' => $_POST['amount'],
            'ipAddress' => $_SERVER['REMOTE_ADDR']
        ]);

        $url = 'payment-form.php?' . http_build_query(['orderId' => $transaction->orderId, 'entranceCode' => $transaction->entranceCode]);

        header('location: ' . $url);
        die();
    } catch (Exception $e) {
        $errors[] = 'Error: ' . $e->getMessage();
    }
}

ob_start();

?>
    <form method="post">
        <div class="form-group">
            <label for="serviceId">ServiceId</label>
            <input type="text" class="form-control" name="serviceId" id="serviceId"
                   aria-describedby="serviceIdHelp" data-lpignore="true"
                   value="<?= @$_SESSION['serviceId'] ?>" placeholder="Ex. SL-1234-5678">
            <small id="serviceIdHelp" class="form-text text-muted">The SL-code of your service, you can find this at: <a
                        href="https://admin.pay.nl/programs/programs">admin.pay.nl</a></small>
        </div>

        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" class="form-control" name="amount" id="amount"
                   aria-describedby="amountHelp"
                   value="1">
            <small id="amountHelp" class="form-text text-muted">The total amount of the transaction (in euro cents)
            </small>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
<?php

include './layout/layout.php';