<?php

namespace Alma\API\Entities\Insurance;

class Subscription
{
    /**
     * @var string
     */
    private $contractId;
    /**
     * @var int
     */
    private $amount;
    /**
     * @var string
     */
    private $cmsReference;
    /**
     * @var int
     */
    private $productPrice;
    /**
     * @var Subscriber
     */
    private $subscriber;

    /**
     * @var $callbackUrl;
     */
    private $callbackUrl;

    public const STATE_STARTED = 'started';
    public const STATE_FAILED = 'failed';
    public const STATE_CANCELLED = 'canceled';
    public const STATE_PENDING = 'pending';
    public const STATE_PENDING_CANCELLATION = 'pending_cancellation';

    /**
     * @param string $contractId
     * @param int  $amount
     * @param string $cmsReference
     * @param int $productPrice
     * @param Subscriber $subscriber
     * @param string $callbackUrl
     */
    public function __construct(
        $contractId,
        $amount,
        $cmsReference,
        $productPrice,
        $subscriber,
        $callbackUrl
    ) {
        $this->contractId = $contractId;
        $this->amount = $amount;
        $this->cmsReference = $cmsReference;
        $this->productPrice = $productPrice;
        $this->subscriber = $subscriber;
        $this->callbackUrl = $callbackUrl;
    }

    public function getAll()
    {
        return [
            $this->getContractId(),
            $this->getAmount(),
            $this->getCmsReference(),
            $this->getProductPrice(),
            $this->getSubscriber(),
            $this->getCallbackUrl(),
        ];
    }

    /**
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * @return string
     */
    public function getContractId()
    {
        return $this->contractId;
    }

    /**
     * @return string
     */
    public function getCmsReference()
    {
        return $this->cmsReference;
    }

    /**
     * @return int
     */
    public function getProductPrice()
    {
        return $this->productPrice;
    }

    /**
     * @return Subscriber
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
