Alma PHP API client
=====================

This is the official PHP API client for Alma.  

**⚠️ Still a work in progress**

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
