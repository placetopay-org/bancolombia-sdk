# Bancolombia Button SDK

Allows to connect with the payments button provided by Bancolombia

Docs https://developer.bancolombia.com/en/node/3252

## Installation

```bash
composer require placetopay/bancolombia-sdk
```

## Usage

### Create an instance of the library

Provide the configuration settings for the process, you can find the client ID in the apps page 
https://developer.bancolombia.com/en/application but the secret will display only when the app is created, after that 
it cannot be obtained again.

Hash is the value that would be translated to commerceTransferButtonId on them. This value will be provided by them

```php
$clientId = 'YOUR APP CLIENT ID';
$clientSecret = 'YOUR APP SECRET';
$hash = 'h4ShG3NER1C';
$bancolombia = \PlacetoPay\BancolombiaSDK\BancolombiaButton::load($clientId, $clientSecret, $hash);
```

### Create a new payment intent

This will return an URL in which the user should be redirected to complete the process

```php
try {
    $result = $bancolombia->request([
        'reference' => 'YOUR_UNIQUE_REFERENCE',
        'description' => 'SOME TEXT TO DISPLAY',
        'amount' => 32178,
        'returnUrl' => 'URL_IN_WHICH_THE_USER_WILL_RETURN',
        'confirmationUrl' => 'URL_TO_RECEIVE_THE_CALLBACK',
    ]);
    
    // Gives the URL to send the user to
    $result->processUrl();
    // Gives the transfer code that identifies this session on Bancolombia
    $result->code();
    // Gives the id for the transference process
    $result->id();
} catch (\PlacetoPay\BancolombiaSDK\Exceptions\BancolombiaException $e) {
    // Handle the exception
}
```

### Handling the callback

Once a transaction is finished a POST call will be sent to the confirmation URL provided previously, to handle that
you just need to make that data into an array and pass it to the SDK

```php
// Assuming that you dont use any framework
$posted = $_POST;

try {
    $result = \PlacetoPay\BancolombiaSDK\BancolombiaButton::handleCallback($posted, 'THE_SECRET');
    // Handle it the same way that querying it
} catch (\PlacetoPay\BancolombiaSDK\Exceptions\BancolombiaException $e) {
    // Signature failed, do not trust this information
}
```

### Query the status of a transference

Although you should really rely on the callback to know the state of a payment, this service also provide you with the state of the payment

```php
try {
    $result = $bancolombia->query('TRANSFER_CODE');
    if ($result->isApproved()) {
        // Well, they pay you, handle it, also you can use the reference and authorization for example to link the info
        // if you require it
    }
    
    if ($result->isPending()) {
        // They can still pay you
    }
    
    if ($result->isRejected()) {
        // There will be no money, you can try it again
    }
} catch (\PlacetoPay\BancolombiaSDK\Exceptions\BancolombiaException $e) {
    // Handle the exception
}
```

### Check the health status of the button

Allows to check if the service is running properly

```php
try {
    $result = $bancolombia->health();
    if ($result->isHealthy()) {
        // All OK
    }
} catch (\PlacetoPay\BancolombiaSDK\Exceptions\BancolombiaException $e) {
    // Handle the exception
}
```
