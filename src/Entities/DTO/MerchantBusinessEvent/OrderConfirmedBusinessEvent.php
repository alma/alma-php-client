<?php

namespace Alma\API\Entities\DTO\MerchantBusinessEvent;


use Alma\API\Exceptions\ParametersException;

class OrderConfirmedBusinessEvent extends AbstractBusinessEvent
{

    /**
     * @var bool
     */
    private $almaP1XStatus;
    /**
     * @var bool
     */
    private $almaBNPLStatus;
    /**
     * @var bool
     */
    private $wasBNPLEligible;
    /**
     * @var string
     */
    private $orderId;
    /**
     * @var string
     */
    private $cartId;
    /**
     * @var string | null
     */
    private $almaPaymentId;


    /**
     * For non alma payment, almaPaymentId should be null
     * For Alma payment, almaPaymentId should be a string
     *
     * @param bool $isAlmaP1X
     * @param bool $isAlmaBNPL
     * @param bool $wasBNPLEligible
     * @param string $orderId
     * @param string $cartId
     * @param string | null $almaPaymentId
     * @throws ParametersException
     */
    public function __construct(bool $isAlmaP1X, bool $isAlmaBNPL, bool $wasBNPLEligible, string $orderId, string $cartId, string $almaPaymentId = null)
    {
        $this->eventType = 'order_confirmed';
        $this->almaP1XStatus = $isAlmaP1X;
        $this->almaBNPLStatus = $isAlmaBNPL;
        $this->wasBNPLEligible = $wasBNPLEligible;
        $this->orderId = $orderId;
        $this->cartId = $cartId;
        $this->almaPaymentId = $almaPaymentId;
        $this->validateData();
    }

    /**
     * @return bool
     */
    public function isAlmaP1X(): bool
    {
        return $this->almaP1XStatus;
    }

    /**
     * @return bool
     */
    public function isAlmaBNPL(): bool
    {
        return $this->almaBNPLStatus;
    }

    /**
     * Was eligible at the time of payment
     *
     * @return bool
     */
    public function wasBNPLEligible(): bool
    {
        return $this->wasBNPLEligible;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getCartId(): string
    {
        return $this->cartId;
    }

    /**
     * @return string | null
     */
    public function getAlmaPaymentId(): ?string
    {
        return $this->almaPaymentId;
    }

    /**
     * Check if it is an Alma payment
     *
     * @return bool
     */
    public function isAlmaPayment(): bool
    {
        return $this->almaP1XStatus || $this->almaBNPLStatus;
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
            (!$this->isAlmaPayment() && !is_null($this->almaPaymentId))
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
}
