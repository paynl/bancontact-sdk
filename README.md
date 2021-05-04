# PAY. Bancontact SDK
## Prerequisites
Before you can use the APIs used by this SDK you need to be PCI DSS Certified.
If you have a PCI CSS Certificate and want to use this SDK, please contact us at support@pay.nl. So we can enable the required APIs for your account. 
## Installation
This SDK can be installed using [composer](https://getcomposer.org/). Run the following command in the root of your application.
```bash
composer require paynl/bancontact-sdk
```

## Usage
### Initialize the gateway
```php
// Require the autoloader.
require_once './vendor/autoload.php';

use Paynl\BancontactSDK\Gateway;

// $gateway gives access to the APIs
$gateway = new Gateway('AT-####-####', '****************************************');

```

### Create a transaction
```php
try{
    $transaction = $gateway->transaction()->start([
        'serviceId' => 'SL-####-####',
        'amount' => 10, // the amount in euro cents
        'ipAddress' => $_SERVER['REMOTE_ADDR'] // the ip address of the customer
    ]);
} catch (Exception $e) {
    // error!
}

// store these somewhere, you need them for all other bancontact calls
$orderId = $transaction->orderId; 
$entranceCode = $transaction->entranceCode;
```
### Get an instance of the Bancontact api
Use the orderId and entranceCode to get an instance of the bancontact API
```php
$bancontact = $gateway->bancontact($orderId, $entranceCode);
```

### Authenticate
Now show a form to the user, you need the following data
- cardHolder
- cardNumber
- expireYear (length = 4)
- expireMont (length = 2)

```php
try{
    $authenticate = $bancontact->authenticate(
        $_POST['cardHolder'], $_POST['cardNumber'], 
        $_POST['expireYear'], $_POST['expireMonth']
    );
} catch(Exception $e){
    // error!
}
 
// This is the url the customer is redirected to afer 3dsecure is complere.
// Iam appending the orderId and entranceCode to the url
$returnUrl = 'https://your-return-url.php?' . http_build_query(['orderId' => $orderId, 'entranceCode' => $entranceCode]);

// this function returns a self submitting form, all you need to do is echo it.
echo $authenticate->get3DSForm($returnUrl);
```
The customer now completes the payment on the website of the bank, when this is finished the customer is redirected to the return url.

### Authorizing the payment
After the customer has authenticated with 3d secure, you'll receive a POST request in the return URL.
Authorize the payment now.
```php
try{
    $bancontact->authorize($_POST['MD'], $_POST['PaRes']);
} catch(Exception $e){
    // error!
}
``` 

### Get the status of a payment
After starting a transaction you can get the current status of a transaction by calling ```$status = $bancontact->getStatus();```
The status object had two variables. ```$status->code``` and ```$status->description```.
The possible status ids are:
- 10 - Pending on start of transaction
- 20 - Input data validated
- 30 - VEReq created
- 40 - VERes received
- 50 - PAReq created
- 60 - PARes received
- 70 - Payer authenticated
- 71 - BSP 1100 message sent
- 72 - BSP 1110 message received
- 80 - Authorized by BSP (but not captured yet)
- 81 - BSP 1220 message sent
- 82 - BSP 1221 message sent
- 83 - BSP 1230 message received
- 84 - BSP 1120 message sent
- 85 - BSP 1121 message sent
- 86 - BSP 1130 message received
- 100 - Transaction processed and captured (after 3DS and BSP completed)
- -10 - Transaction canceled (by cardholder)
- -20 - Transaction failed - in case of invalid response from Directory Server (3DS) or Switch (BSP)
- -21 - Transaction failed - in case of invalid response from Directory Server (3DS) or Switch (BSP)



## Demo Application
This SDK also contains a demo application.
In order to run the demo application, follow these steps:

- Checkout this repository
- Run ```composer install``` to install the dependencies
- Run ```composer demo``` to start the demo application
- The demo application is now accessible from the browser on http://localhost:8080

The demo application asks for the credentials (tokenCode, token and serviceId) and stores them in the session. DO NOT DO THIS IN PRODUCTION!
