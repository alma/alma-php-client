Alma PHP API client
=====================

This is the official PHP API client for [Alma](https://getalma.eu).

This PHP API Client is being used in production on thousands of e-commerce websites and provides the necessary
endpoints to build a full-fledge integration.
It does not, however, implement the full Alma API as [documented here](https://api.getalma.eu/docs) yet.
If you find yourself needing to use some endpoints that are not yet implemented, feel free to reach out! (or even better, submit a PR :))

Installation
------------

The Alma PHP API Client library is tested against all recently supported PHP versions.
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

An example of using the API client. (check [API documentation](https://docs.getalma.eu/reference/) for further information)

### 1. instanciate client in test mode

```php
$alma = new Alma\API\Client($apiKey, ['mode' => Alma\API\Client::TEST_MODE]);
```

### 2. check eligibility

```php
// ...
$amountInCents = 150000; // 1500 euros
$customerBillingCountry = ""; // can be an empty string but NOT null
$customerShippingCountry = "FR"; // billing_address has priority over shipping_address (if not empty)
try {
    $eligibilities = $alma->payments->eligibility(
        [
            'purchase_amount' => $amountInCents,
            'billing_address' => [ // (optional) useful to check eligibility for a specific billing country
                'country' => $customerBillingCountry // can be an empty string but not null
            ],
            'shipping_address' => [ // (optional) useful to check eligibility for a specific shipping country
                'country' => $customerShippingCountry
            ],
            'queries'         =>
                [
                    [
                        'installments_count' => 1,
                        'deferred_days'      => 30,
                    ],
                    [
                        'installments_count' => 2,
                    ],
                    [
                        'installments_count' => 3,
                    ],
                    [
                        'installments_count' => 4,
                    ],
                    [
                        'installments_count' => 10,
                    ],
                ],
        ],
        $raiseOnError = true // throws an exception on 4xx or 5xx http return code
                             // instead of just returning an Eligibility Object with isEligible() === false
    );
} catch (Alma\API\RequestError $error) {
    header("HTTP/1.1 500 Internal Server Error");
    die($error->getMessage());
}

foreach($eligibilities as $eligibility) {
    if (!$eligibility->isEligible()) {
        die('cart is not eligible');
    }
}
// ...
```

### 3. check available fee plans and build payment form

```php
// ...
echo "<form>";
echo "<h2>Available feePlans are:</h2>";
foreach($alma->merchants->feePlans($kind = FeePlan::KIND_GENERAL, $installmentsCounts = "all", $includeDeferred = true) as $feePlan) {
    if (!$feePlan->allowed) {
        continue;
    }
    printf('<label for="%s">Pay in %s by %s installments count</label>', $feePlan->getPlanKey(), $feePlan->getDeferredDays(), $feePlan->getInstallmentsCount());
    printf('<input id="radio-%s" type="radio" name="fee-plan" value="%s">', $feePlan->getPlanKey(), $feePlan->getPlanKey());
}
echo "<button type=\"submit\">Submit</button>";
echo "</form>";
// ...
```

You can prefer use eligibilities to do this work but this part of code allow you to get more familiar with feePlans definitions.


### 4. build a payment plan

```php
// ...
function formatMoney(int $amount) {
    return sprintf("%.2f %s", round(intval($amount) / 100, 2), "€");
}
function formatPercent(int $amount) {
    return sprintf("%.2f %s", round(intval($amount) / 100, 2), "%");
}
// ...
foreach($eligibilities as $eligibility) {
    // display following payment plan (or not eligible message) on feePlan selection using javascript.
    printf('<div id="table-%s">', $eligibility->getPlanKey());
    if (!$eligibility->isEligible()) {
        echo "This fee plan is not eligible!";
        echo "</div>";
        continue;
    }
    if (!$paymentPlans = $eligibility->getPaymentPlan()) {
        echo "No payment plan found for current eligibility! (that should not happen)";
        echo "</div>";
        continue;
    }
    echo "<ul>";
    foreach ($paymentPlans as $paymentPlan) {
        $planDefinition     = sprintf(
            "<li>You will pay %s on %s including %s fees & %s of interest</li>",
            formatMoney($paymentPlan['total_amount']),
            (new DateTime())->setTimestamp($paymentPlan['due_date'])->format('Y-m-d'),
            formatMoney($paymentPlan['customer_fee']),
            formatMoney($paymentPlan['customer_interest'])
        );
    }
    echo "</ul>";
    echo "    <div>";
    echo "    Annual Interest Rate:" . formatPercent($eligibility->getAnnualInterestRate()) . "<br>";
    echo "    Order Amount:" . formatMoney($amountInCents);
    echo "    Total Cost Amount:" . formatMoney($eligibility->getCustomerTotalCostAmount());
    echo "    </div>";
    echo "</div>";
}
// ...
```

### 5. create a payment and redirecting a customer to the payment page

```php
// ...
$payment = $alma->payments->createPayment(
    [
        'origin'   => 'online',
        'payment'  =>
            [
                'return_url'         => '<where_the_customer_will_be_redirect_after_alma_checkout>',
                'ipn_callback_url'   => '<your_ipn_callback_url>',
                'purchase_amount'    => 150000,
                'installments_count' => 4,
                'custom_data'        =>
                    [
                        'my_very_important_key' => '<the_context_custom_value>',
                    ],
                'locale'             => 'fr',
                'billing_address'    =>
                    [
                        'first_name'  => 'John',
                        'last_name'   => 'Doe',
                        'email'       => 'john-doe@yopmail.fr',
                        'line1'       => '1 rue de Rome',
                        'postal_code' => '75001',
                        'city'        => 'Paris',
                        'country'     => 'FR',
                    ],
                'shipping_address'   =>
                    [
                        'first_name'  => 'John',
                        'last_name'   => 'Doe',
                        'email'       => 'john-doe@yopmail.fr',
                        'line1'       => '1 rue de Rome',
                        'postal_code' => '75001',
                        'city'        => 'Paris',
                        'country'     => 'FR',
                    ],
            ],
        'customer' =>
            [
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'john-doe@yopmail.fr',
                'phone'      => '06 12 34 56 78',
                'addresses'  =>
                    [
                        [
                            'first_name' => 'John',
                            'last_name'  => 'Doe',
                            'email'      => 'john-doe@yopmail.fr',
                            'phone'      => '06 12 34 56 78',
                        ],
                    ],
            ],
    ]
);

// store $payment->id and link it to the customer order here ;)

header('Location: ' . $payment->url);
exit();
// ...
```

### 6. receive notification about payment validation by IPN

(can be given on payment creation or statically defined in your Alma Dashboard)

```php
// ...
if (!isset($_GET['pid']) || empty($_GET['pid'])) {
     header("HTTP/1.1 400 Bad Request");
     die();
}
// retrieve your local order by payment id
$order = getOrderByPaymentId($_GET['pid'])
if (!$order) {
     header("HTTP/1.1 404 Not Found");
     die();
}

// check $payment->state & do the order / customer stuff you want here :D

header("HTTP/1.1 200");
exit();
// ...
```

### 7. retrieve payment information and display status

```php
// ...
$payment = $alma->payments->fetch($paymentId);
switch($payment->state) {
    case Alma\API\Entities\Payment::STATE_IN_PROGRESS: break;
    case Alma\API\Entities\Payment::STATE_PAID: break;
}
// ...
```

License
-------

Alma PHP Client is distributed under [the MIT license](LICENSE).
