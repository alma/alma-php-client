# CHANGELOG

## v2.2.0 - 2024-09-05

### Changes

### 🚀 New Features

- Add function for HMAC verification (#132)

#### Contributors

@Benjamin-Freoua-Alma, @CamilleFljt, @Francois-Gomis and @carine-bonnafous

## v2.1.0 - 2024-07-29

### Changes

- Add new release workflow (#111)
- Improve unit tests (#110)
- Rework integration test in php client (#125)

### 🚀 New Features

- Add updateTracking put endpoint & refactor Order Model (#124)

#### Contributors

@Francois-Gomis, @carine-bonnafous, @gdraynz, @joyet-simon and @remic-alma

## v2.0.7

* Add endpoint Order State

## v2.0.6

* Fix error in throw exception without sprintf

## v2.0.5

* Fix endpoint customer-carts

## v2.0.4

* Fix json_decode in getSubscriptionDetails
* Add subscription pending_cancellation status

## v2.0.3

* Added order_id in subscription model
* Added sendCustomerCart endpoint Insurance
* Added cancelSubscription endpoint Insurance

## v2.0.2

* Added getSubscription endpoint Insurance
* Fix Subscription amount model

## v2.0.1

* Fix : Don't remove amount in refund if value equal zero

## v2.0.0

* Added Alma insurance endpoints

## v1.11.2

* Fix : Compatibility psr/log 1/2/3
* Fix : Unit tests and Integration tests for PHP5.6 to PHP8.2

## v1.11.1

* Fix : Check the amount purchase type
* Fix: composer

## v1.11.0

* Fix : Compatibility with PHP 8.2

## v1.10.0

* Added pay now payment plan behavior

## v1.9.3

* Added payment cancel endpoint

## v1.9.2

* Fixed tests PSR4 namespaces

## v1.9.1

* Added `addConsent` and `removeConsent` method to `ShareOfCheckout` endpoint

## v1.9.0

* Added `expired_at` property to `Payment` entity
* Added `getErrorMessage` method to `RequestError` to fetch error message from the response when not accessible from `getMessage`

## v1.8.0

* Splited `refund` endpoint into `partialRefund` and `fullRefund`
* Added github CI for lint and unit tests purposes
* Added unit-tests
* Extract RequestError from Request.php file
* Added share of checkout endpoint

## v1.7.1

* Fix merchant reference only on partial refunds (#23)

## v1.7.0

* Allow merchant reference on refund endpoint

## v1.6.0

* Add payment upon trigger fields
* Add payment customer field
* Add payment billing address field

## v1.5.1

* Added configuration validation
* Utils functions now moved into classes to respect php good practices
* Added docker-compose to ease dev and local testing

## v1.5.0

* Add trigger payment in endpoint payment

## v1.4.0

* Add edit payment in endpoint payment

## v1.3.1

* Add annual interest rate from eligibility

## v1.3.0

* Add P10x informations

## v1.2.0

* New eligibility v2 endpoint
* Add customerTotalCostAmount, customerTotalCostBps in eligibility

## v1.1.0

* Add an option to depart from a legacy behaviour where the eligibility endpoint would not raise RequestErrors on 4xx
  and 5xx errors. The default is to keep the original behaviour so as not to break existing implementations.
  New implementations should call the endpoint with a second argument set to `true` and try/catch RequestError
  exceptions to better handle error cases with eligibility:
  ```php
    try {
      $eligibility = $alma->payments->eligibility($data, true);
    } catch (RequestError $e) {
        // Handle errors
    }
  
  
  ```
* Add fields and docs to the Payment entity
* Add a Refund entity and extract refunds data within the Payment entity constructor
* Add the `feePlans` endpoint to the `merchants` scope, so that one can fetch "fee plans" configured for their merchant
  account: `$alma->merchants->feePlans();` — see function's docs for available options

## v1.0.15

* Add missing fields to Instalment entity

## v1.0.14

* Fix non-working code in the Orders endpoint & PaginatedResults class
* Improve type hints
* Fix compatibility with PHP versions older than 5.6

## v1.0.13

* Move `LIVE_MODE` & `TEST_MODE` constants into the `Client` class so that they're more easily addressable using
  `Alma\API\Client::LIVE_MODE` & `Alma\API\Client::TEST_MODE`

## v1.0.12

* Fixes webhook signature computation (use url-safe base64 encoding)
* Updates Webhook type constant name

## v1.0.11

* Adds endpoint to send payment link to customer via SMS

## v1.0.10

* Properly deserialize orders array in Payment entity
* Documents Order entity fields
* Adds endpoint to add an Order to a Payment

## v1.0.9

* Document usage of the lib without Composer & release a ready-to-use zip of the API client

## v1.0.8

* Fixes handling of server errors in eligibility endpoint

## v1.0.7

* Fixes eligibility response handling, which was buggy in case of non-eligibility responses and legacy response types

## v1.0.6

* Minor bug fix

## v1.0.5

* Fixes missing Webhooks endpoint instance on Client class

## v1.0.4

* Adds the Orders endpoint and Order entity to handle orders associated to a payment
* Improves the eligibility endpoint to handle multiple eligibility results (for different installments counts)
* Adds the Webhooks endpoint to be able to register webhook URLs against the API

## v1.0.3

* Adds payment_plan to eligibility result

## v1.0.2

* Bug fix: Always include a body for POST requests to prevent HTTP 411 error

## v1.0.1

* Adds `fee_plans` attribute to the `Merchant` entity
* Deprecate `Payments::createPayment` in favor of `Payments::create`
* Adds the `Payments::refund` endpoint to partially or totally refund a payment
* [Adds PHPUnit and a few unit tests, but nothing big just yet]

## v1.0.0

Getting more serious with a 1.0.0 release! 🎉

* Adds User-Agent with PHP and client version
* Adds ability to add User-Agent "components" to the request

## v0.0.7

* Eligibility check now returns `200 OK` with `{"eligible": false}` for non-eligible purchases:
  supports legacy `406` status code and the new version
* New eligibility check's negative response includes constraints that should be met to be eligible

## v0.0.6

* Adds the possibility to flag a payment as potentially fraudulent

## v0.0.5

* Adds `Alma\API\Payment::STATE_PAID`

## v0.0.4

* Adds support for Sandbox API root

## v0.0.3

* Bug fixes:
  * Missing default Logger in Client instantiation - now using Psr\Log\NullLogger
  * Alma\Api\Entity\Payment attributes had been wrongly converted to `camelCase`
  

## v0.0.2

* Updates root namespace to `Alma\API` instead of just `Alma`
* Adds PSR-4 autoload config to `composer.json`
* Logger is now just an option to the Client creation
* Uses PSR-3 logger spec

## v0.0.1

* Initial "pre-release" of the API Client
  
* Includes two main endpoints: Payments and Merchants
  
* Provides what's necessary for a typical payment flow:
  
  * `Payments.createPayment` and `Payments.eligibility`
  * `Merchants.me`
  
* Base `Alma\API\Client` class can be configured with API key and live/test mode
  
* TLS is automatically forced to TLS 1.2 to meet Alma's security requirements, but configurable
  
