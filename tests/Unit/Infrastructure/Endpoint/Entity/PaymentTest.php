<?php

namespace Alma\API\Tests\Unit\Infrastructure\Endpoint\Entity;

use Alma\API\Domain\Entity\OrderList;
use Alma\API\Domain\Entity\Payment;
use Alma\API\Domain\Entity\PaymentPlan;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{

    public function testPaymentConstruct()
    {
        $paymentData = $this->getPaymentData();
        $payment = new Payment($paymentData);
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals($paymentData['amount_already_refunded'], $payment->getAmountRefunded());
        $this->assertEquals($paymentData['custom_data'], $payment->getCustomData());
        $this->assertEquals($paymentData['customer_fee'], $payment->getCustomerFee());
        $this->assertEquals($paymentData['customer_interest'], $payment->getCustomerInterest());
        $this->assertEquals($paymentData['deferred_days'], $payment->getDeferredDays());
        $this->assertEquals($paymentData['deferred_months'], $payment->getDeferredMonths());
        $this->assertEquals($paymentData['expired_at'], $payment->getExpiredAt());
        $this->assertEquals($paymentData['id'], $payment->getId());
        $this->assertEquals($paymentData['installments_count'], $payment->getInstallmentsCount());
        $this->assertEquals($paymentData['kind'], $payment->getKind());
        $this->assertInstanceOf(OrderList::class, $payment->getOrders());
        $this->assertEquals($paymentData['purchase_amount'], $payment->getPurchaseAmount());
        $this->assertEquals($paymentData['state'], $payment->getState());
        $this->assertEquals($paymentData['url'], $payment->getUrl());

        $this->assertInstanceOf( PaymentPlan::class, $payment->getPaymentPlan());
        $this->assertEquals($paymentData['installments_count'],$payment->getPaymentPlan()->getInstallmentCount());
    }
    private function getPaymentData()
    {
        return [
            "amount_already_refunded" => 1200,
            "billing_address" => [
                "city" => "Monaco",
                "company" => "",
                "country" => "FR",
                "county_sublocality" => null,
                "created" => 1757407747,
                "email" => "louis.gonzague1@gmail.com",
                "first_name" => "Louis",
                "last_name" => "Gonzague",
                "line1" => "12 rue nationale",
                "line2" => "",
                "phone" => "+33600000000",
                "postal_code" => "98000",
                "state_province" => "",
                "title" => null
            ],
            "cancelation_reason" => null,
            "cart" => [
                "items" => [
                    [
                        "line_price" => 10000,
                        "picture_url" => "https://img01.ztat.net/article/spp-media-p1/c5daad78aae54382848508b3873ae5d6/5fcb614869614bd0a8ec40e08fa3ae9a.jpg?imwidth=1800&filter=packshot",
                        "quantity" => 1,
                        "title" => "My super item"
                    ]
                ]
            ],
            "country_of_service" => "FR",
            "created" => 1757407747,
            "custom_data" => [
                "order_id" => 18491,
                "order_key" => "wc_order_Vat4darb9ui0M"
            ],
            "customer" => [
                "business_id_number" => null,
                "business_name" => null,
                "created" => 1757407747,
                "email" => "louis.gonzague1@gmail.com",
                "first_name" => "Louis",
                "id" => "customer_xxxxxxxxxxxxxxxxxxx",
                "is_business" => false,
                "last_name" => "Gonzague",
                "phone" => null
            ],
            "customer_cancel_url" => "https://commander/?pid=payment_xxxxxxxxxxxxxxxxx",
            "customer_fee" => 816,
            "customer_fees_refunded" => 0,
            "customer_interest" => 715,
            "deferred_days" => 1,
            "deferred_months" => 2,
            "expired_at" => 1757407756,
            "failure_return_url" => null,
            "fees" => [
                "merchant" => [
                    "tax" => 197,
                    "total" => 1186,
                    "total_excluding_tax" => 989
                ]
            ],
            "id" => "payment_xxxxxxxxxxxxxxxxx",
            "installments_count" => 3,
            "integration_origin" => "Shopify",
            "ipn_callback_url" => "https://www.test.com/wc-api/alma_ipn_callback/?pid=payment_xxxxxxxxxxxxxxxxx",
            "is_completely_refunded" => false,
            "is_deferred_capture" => false,
            "kind" => "P3X",
            "locale" => "fr",
            "merchant_id" => "merchant_xxxxxxxxxxxxxxxxxx",
            "merchant_name" => "ECOM Inte Shop",
            "merchant_target_fee" => 1186,
            "orders" => [
                [
                    "comment" => null,
                    "created" => 1757407747,
                    "customer_url" => null,
                    "data" => [],
                    "id" => "order_xxxxxxxxxxxxxxxxxxxx",
                    "merchant_reference" => "18491",
                    "merchant_url" => null,
                    "payment" => ""
                ]
            ],
            "origin" => "online",
            "payment_plan" => [
                [
                    "customer_fee" => 816,
                    "customer_interest" => 0,
                    "date_paid" => null,
                    "due_date" => 1757407747,
                    "is_check" => false,
                    "original_purchase_amount" => 15829,
                    "purchase_amount" => 15829,
                    "state" => "pending"
                ],
                [
                    "customer_fee" => 0,
                    "customer_interest" => 0,
                    "date_paid" => null,
                    "due_date" => 1759999747,
                    "is_check" => false,
                    "original_purchase_amount" => 15829,
                    "purchase_amount" => 15829,
                    "state" => "pending"
                ],
                [
                    "customer_fee" => 0,
                    "customer_interest" => 0,
                    "date_paid" => null,
                    "due_date" => 1762678147,
                    "is_check" => false,
                    "original_purchase_amount" => 15829,
                    "purchase_amount" => 15829,
                    "state" => "pending"
                ]
            ],
            "processing_status" => "awaiting_authorization",
            "purchase_amount" => 47487,
            "refunds" => [],
            "return_url" => "https://www.test.com/wc-api/alma_customer_return/?pid=payment_xxxxxxxxxxxxxxxxx",
            "seller" => null,
            "shipping_address" => [
                "city" => "Monaco",
                "company" => "",
                "country" => "FR",
                "county_sublocality" => null,
                "created" => 1757407747,
                "email" => null,
                "first_name" => "Louis",
                "last_name" => "Gonzague",
                "line1" => "12 rue nationale",
                "line2" => "",
                "phone" => null,
                "postal_code" => "98000",
                "state_province" => "",
                "title" => null
            ],
            "state" => "not_started",
            "transaction_country" => "FR",
            "updated" => 1757407748,
            "url" => "https://checkout.sandbox.getalma.eu/payment_xxxxxxxxxxxxxxxxx"
        ];
    }
}