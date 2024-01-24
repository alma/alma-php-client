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
     * @param string $contractId
     * @param string $cmsReference
     * @param int $productPrice
     * @param Subscriber $subscriber
     */
    public function __construct(
        $contractId,
        $cmsReference,
        $productPrice,
        $subscriber
    )
    {
        $this->contractId = $contractId;
        $this->cmsReference = $cmsReference;
        $this->productPrice = $productPrice;
        $this->subscriber = $subscriber;
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