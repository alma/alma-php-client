Alma PHP API client
=====================

This is the official PHP API client for Alma.  

**⚠️ Still a work in progress**

Installation
------------

The Alma PHP API Client library requires at least PHP 5.6.
A modern, [supported PHP version](https://www.php.net/supported-versions.php) is highly recommended.

### Composer
You would normally install this package via Composer:

```
composer require alma/alma-php-client
```

### Without Composer

* Head over to the [releases](https://github.com/alma/alma-php-client/releases) and grab the `alma-php-client.zip` file of
the latest published library version.
* Unzip the library into your vendors directory. 
* Require the included Composer's autoload file:

```php
require_once "path/to/alma-php-client/vendor/autoload.php";
```

* You should then be able to use Alma as if it was installed with Composer.

Typical usage
-------------

An example of using the API client for creating a payment and redirecting a customer to the payment page:
```php
$alma = new Alma\API\Client($apiKey, ['mode' => Alma\API\TEST_MODE]);

// Fetch payment data from your backend
$paymentData = dataFromCart();
$eligibility = $alma->payments->eligibility($paymentData);

if($eligibility->isEligible) {
    $payment = $alma->payments->createPayment($paymentData);
    redirect_to($payment->url);    
}
```

License
-------

Alma PHP Client is distributed under [the MIT license](LICENSE). 
