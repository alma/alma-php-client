<?php
namespace Alma\API\Tests\Unit\Entity;

use Alma\API\Entity\Merchant;
use PHPUnit\Framework\TestCase;

class MerchantTest extends TestCase
{

    public function testInstalmentConstructCanCreatePayment()
    {
        $merchant = new Merchant($this->getMockMerchantData());
        $this->assertInstanceOf(Merchant::class, $merchant);
        $this->assertEquals("merchant_1234567890abcdef", $merchant->getId());
        $this->assertEquals("ALMA Inte Shop", $merchant->getName());
        $this->assertTrue($merchant->canCreatePayments());
    }
    public function testInstalmentConstructCanNotCreatePayment()
    {
        $merchantData = $this->getMockMerchantData();
        $merchantData["can_create_payments"] = false;
        $merchant = new Merchant($merchantData);
        $this->assertFalse($merchant->canCreatePayments());
    }

    private function getMockMerchantData(): array
    {
        return [
            "id" => "merchant_1234567890abcdef",
            "name" => "ALMA Inte Shop",
            "country" => "FR",
            "website" => "",
            "customer_cancel_url" => null,
            "ipn_callback_url" => null,
            "legal_validation_state" => "verified",
            "state" => "not_verified",
            "legal_entity" => [
                "id" => "legal_entity_1234567890abcdef",
                "type" => "",
                "business_name" => "",
                "vat_number" => null,
                "business_tax_id" => "",
                "is_complementary_kyc" => false,
                "validation_status" => "pending",
                "address" => [
                    "title" => null,
                    "first_name" => null,
                    "last_name" => null,
                    "company" => null,
                    "line1" => null,
                    "line2" => null,
                    "city" => null,
                    "postal_code" => null,
                    "county_sublocality" => null,
                    "state_province" => null,
                    "email" => null,
                    "phone" => null,
                    "country" => null
                ],
                "legal_proof" => null
            ],
            "can_create_payments" => true,
            "can_pay_in" => true,
            "can_pay_out" => true,
            "use_stonly_onboarding" => false,
            "cms_allow_inpage" => true,
            "cms_insurance" => true,
            "payout_label" => null,
            "can_refund" => true,
            "minimum_purchase_amount" => 5000,
            "maximum_purchase_amount" => 200000,
            "maximum_purchase_exposure" => 150000,
            "accepted_tos" => true,
            "fee_plans" => [
                [
                    "allowed" => true,
                    "available_in_pos" => true,
                    "available_online" => false,
                    "customer_fee_variable" => 50,
                    "deferred_days" => 0,
                    "deferred_months" => 0,
                    "deferred_trigger_bypass_scoring" => false,
                    "deferred_trigger_limit_days" => null,
                    "first_installment_ratio" => null,
                    "installments_count" => 2,
                    "kind" => "general",
                    "max_purchase_amount" => 200000,
                    "merchant" => "merchant_11xYpTY1GTkww5uWFKFdOllK82S1r7j5v5",
                    "merchant_fee_variable" => 310,
                    "merchant_fee_fixed" => 0,
                    "min_purchase_amount" => 5000
                ],
                [
                    "allowed" => true,
                    "available_in_pos" => true,
                    "available_online" => true,
                    "customer_fee_variable" => 380,
                    "deferred_days" => 0,
                    "deferred_months" => 0,
                    "deferred_trigger_bypass_scoring" => false,
                    "deferred_trigger_limit_days" => null,
                    "first_installment_ratio" => null,
                    "installments_count" => 3,
                    "kind" => "general",
                    "max_purchase_amount" => 200000,
                    "merchant" => "merchant_11xYpTY1GTkww5uWFKFdOllK82S1r7j5v5",
                    "merchant_fee_variable" => 0,
                    "merchant_fee_fixed" => 0,
                    "min_purchase_amount" => 5000
                ],
                [
                    "allowed" => true,
                    "available_in_pos" => true,
                    "available_online" => true,
                    "customer_fee_variable" => 0,
                    "deferred_days" => 0,
                    "deferred_months" => 0,
                    "deferred_trigger_bypass_scoring" => false,
                    "deferred_trigger_limit_days" => null,
                    "first_installment_ratio" => null,
                    "installments_count" => 4,
                    "kind" => "general",
                    "max_purchase_amount" => 200000,
                    "merchant" => "merchant_11xYpTY1GTkww5uWFKFdOllK82S1r7j5v5",
                    "merchant_fee_variable" => 480,
                    "merchant_fee_fixed" => 0,
                    "min_purchase_amount" => 5000
                ]
            ],
            "adyen_merchant_account" => null,
            "onboarding_information" => [
                "country" => null
            ]
        ];
    }
}