<?php
namespace Alma\API\Tests\Unit\Infrastructure\Endpoint\Entity;

use Alma\API\Domain\Entity\Installment;
use PHPUnit\Framework\TestCase;

class InstallmentTest extends TestCase
{
    public function testInstalmentConstruct()
    {
        $installment = new Installment([
            "customer_fee" => 816,
            "customer_interest" => 0,
            "date_paid" => null,
            "due_date" => 1757407747,
            "is_check" => false,
            "original_purchase_amount" => 15820,
            "purchase_amount" => 15829,
            "state" => "pending",
            "unknown_field" => "unknown_value"
        ]);
        $this->assertInstanceOf(Installment::class, $installment);
        $this->assertEquals(816, $installment->getCustomerFee());
        $this->assertEquals(0, $installment->getCustomerInterest());
        $this->assertEquals(1757407747, $installment->getDueDate());
        $this->assertEquals(15829, $installment->getPurchaseAmount());
        $this->assertEquals("pending", $installment->getState());
    }
}