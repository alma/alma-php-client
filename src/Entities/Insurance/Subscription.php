<?php

namespace Alma\API\Entities\Insurance;

class Subscription
{
    /**
     * @var string
     */
    private $contractId;
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
     * @var $cancelUrl;
     */
    private $cancelUrl;

    /**
     * @param string $contractId
     * @param string $cmsReference
     * @param int $productPrice
     * @param Subscriber $subscriber
     */
    public function __construct(
        $contractId,
        $cmsReference,
        $productPrice,
        $subscriber,
        $cancelUrl
    )
    {
        $this->contractId = $contractId;
        $this->cmsReference = $cmsReference;
        $this->productPrice = $productPrice;
        $this->subscriber = $subscriber;
        $this->cancelUrl = $cancelUrl;
    }

    public function getAll()
    {
        return [
            $this->contractId,
            $this->cmsReference,
            $this->productPrice,
            $this->subscriber,
            $this->cancelUrl
        ];
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
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
}