<?php

namespace Alma\API\Entities\DTO\MerchantBusinessEvent;


use Alma\API\Exceptions\ParametersException;

class OrderConfirmedBusinessEvent extends AbstractBusinessEvent
{

    /**
     * @var bool
     */
    private $isAlmaP1X;
    /**
     * @var bool
     */
    private $isAlmaBNPL;
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
     * @throws ParametersException
     */
    public function __construct($isAlmaP1X, $isAlmaBNPL, $wasBNPLEligible, $orderId, $cartId, $almaPaymentId = null)
    {
        $this->eventType = 'order_confirmed';
        $this->isAlmaP1X = $isAlmaP1X;
        $this->isAlmaBNPL = $isAlmaBNPL;
        $this->wasBNPLEligible = $wasBNPLEligible;
        $this->orderId = $orderId;
        $this->cartId = $cartId;
        $this->almaPaymentId = $almaPaymentId;
        $this->validateData();
    }

    /**
     * @return bool
     */
    public function getIsAlmaP1X()
    {
        return $this->isAlmaP1X;
    }

    /**
     * @return bool
     */
    public function getIsAlmaBNPL()
    {
        return $this->isAlmaBNPL;
    }

    /**
     * @return bool
     */
    public function getWasBNPLEligible()
    {
        return $this->wasBNPLEligible;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return string | null
     */
    public function getAlmaPaymentId()
    {
        return $this->almaPaymentId;
    }

    /**
     * Check if it is an Alma payment
     *
     * @return bool
     */
    public function isAlmaPayment()
    {
        return $this->isAlmaP1X || $this->isAlmaBNPL;
    }

    /**
     * @return void
     * @throws ParametersException
     */
    protected function validateData()
    {
        if(
            !is_bool($this->isAlmaP1X) ||
            !is_bool($this->isAlmaBNPL) ||
            !is_bool($this->wasBNPLEligible) ||
            !is_string($this->orderId) ||
            !is_string($this->cartId) ||
            // Alma payment id should be absent for non Alma payments
            (!$this->isAlmaPayment() && !is_null($this->almaPaymentId))
        )
        {
            throw new ParametersException('Invalid data type in OrderConfirmedBusinessEvent constructor');
        }

        //Alma payment id for Alma payment, Must be a string
        if(
            ($this->isAlmaPayment()) &&
            !is_string($this->almaPaymentId)
        )
        {
            throw new ParametersException('Alma payment id is mandatory for Alma payment');
        }
    }


}