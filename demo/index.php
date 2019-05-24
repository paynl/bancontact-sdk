<?php
require_once '../vendor/autoload.php';
session_start();
if (isset($_POST['tokenCode']) && isset($_POST['apiToken'])) {
    // Normally you'd use a config for this, dont save in session!
    $_SESSION['tokenCode'] = $_POST['tokenCode'];
    $_SESSION['apiToken'] = $_POST['apiToken'];
    header('location: new-transaction.php');
    die();
}


/** For DEMO purposes only, token and tokencode should be loaded from a config file. */
ob_start();
?>
    <form method="post">
        <div class="form-group">
            <label for="tokenCode">Token Code</label>
            <input type="text" class="form-control" name="tokenCode" id="tokenCode"
                   aria-describedby="tokenCodeHelp"
                   value="<?= @$_SESSION['tokenCode'] ?>" placeholder="Ex. AT-1234-5678">
            <small id="tokenCodeHelp" class="form-text text-muted">You can find your token-code at <a
                        href="https://admin.pay.nl/company/tokens">admin.pay.nl</a></small>
        </div>
        <div class="form-group">
            <label for="apiToken">API token</label>
            <input type="text" class="form-control" id="apiToken" name="apiToken" value="<?= @$_SESSION['apiToken'] ?>"
                   aria-describedby="apiTokenHelp">
            <small id="apiTokenHelp" class="form-text text-muted">You can find your API token at <a
                        href="https://admin.pay.nl/company/tokens">admin.pay.nl</a></small>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>


<?php
include './layout/layout.php';

