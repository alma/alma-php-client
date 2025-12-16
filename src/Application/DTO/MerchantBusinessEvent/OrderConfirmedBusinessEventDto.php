<?php

namespace Alma\API\Application\DTO\MerchantBusinessEvent;


use Alma\API\Infrastructure\Exception\ParametersException;

class OrderConfirmedBusinessEventDto
{
    private const EVENT_TYPE = 'order_confirmed';

    /**
     * @var bool
     */
    private bool $isAlmaP1X;
    /**
     * @var bool
     */
    private bool $isAlmaBNPL;
    /**
     * @var bool
     */
    private bool $wasBNPLEligible;
    /**
     * @var string
     */
    private string $orderId;
    /**
     * @var string
     */
    private string $cartId;
    /**
     * @var string | null
     */
    private ?string $almaPaymentId;


    /**
     * For non alma payment, almaPaymentId should be null
     * For Alma payment, almaPaymentId should be a string
     *
     * @param bool $isAlmaP1X Whether the order was paid with Alma P1X
     * @param bool $isAlmaBNPL Whether the order was paid with Alma BNPL
     * @param bool $wasBNPLEligible Whether the order was eligible for BNPL
     * @param string $orderId The order identifier
     * @param string $cartId The cart identifier
     * @param string|null $almaPaymentId Mandatory for Alma payments, should be null for non Alma payments
     * @throws ParametersException
     */
    public function __construct(bool $isAlmaP1X, bool $isAlmaBNPL, bool $wasBNPLEligible, string $orderId, string $cartId, string $almaPaymentId = '')
    {
        $this->setIsAlmaP1X($isAlmaP1X);
        $this->setIsAlmaBNPL($isAlmaBNPL);
        $this->setWasBNPLEligible($wasBNPLEligible);
        $this->setOrderId($orderId);
        $this->setCartId($cartId);
        $this->setAlmaPaymentId($almaPaymentId);
        $this->validateData();
    }

    /**
     * Check if it is an Alma payment
     *
     * @return bool
     */
    public function isAlmaPayment(): bool
    {
        return $this->isAlmaP1X || $this->isAlmaBNPL;
    }

    /**
     * @return void
     * @throws ParametersException
     */
    protected function validateData()
    {
        if (
            (empty($this->orderId)) ||
            (empty($this->cartId)) ||
            // Alma payment id should be absent for non Alma payments
            (!$this->isAlmaPayment() && !empty($this->almaPaymentId))
        )
        {
            throw new ParametersException('Invalid data type in OrderConfirmedBusinessEvent constructor');
        }
        //Alma payment id for Alma payment, Must be a string
        if (
            $this->isAlmaPayment() &&
            (!is_string($this->almaPaymentId) || empty($this->almaPaymentId))
        )
        {
            throw new ParametersException('Alma payment id is mandatory for Alma payment');
        }
    }

    private function setIsAlmaP1X(bool $isAlmaP1X): void
    {
        $this->isAlmaP1X = $isAlmaP1X;
    }

    private function setIsAlmaBNPL(bool $isAlmaBNPL): void
    {
        $this->isAlmaBNPL = $isAlmaBNPL;
    }

    private function setWasBNPLEligible(bool $wasBNPLEligible): void
    {
        $this->wasBNPLEligible = $wasBNPLEligible;
    }

    private function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    private function setCartId(string $cartId): void
    {
        $this->cartId = $cartId;
    }

    private function setAlmaPaymentId(string $almaPaymentId): void
    {
        $this->almaPaymentId = $almaPaymentId;
    }

    public function toArray(): array
    {
        return array_filter([
            'event_type' => self::EVENT_TYPE,
            'is_alma_p1x' => $this->isAlmaP1X,
            'is_alma_bnpl' => $this->isAlmaBNPL,
            'was_bnpl_eligible' => $this->wasBNPLEligible,
            'order_id' => $this->orderId,
            'cart_id' => $this->cartId,
            'alma_payment_id' => $this->almaPaymentId
        ], function($value) {
            return $value !== null && $value !== '' && !(is_array($value) && empty($value));
        });
    }
}
