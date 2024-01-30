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
     * @var $callbackUrl;
     */
    private $callbackUrl;
    /**
     * @var string
     */
    private $callbackToken;

    /**
     * @param string $contractId
     * @param string $cmsReference
     * @param int $productPrice
     * @param Subscriber $subscriber
     * @param string $callbackUrl
     */
    public function __construct(
        $contractId,
        $cmsReference,
        $productPrice,
        $subscriber,
        $callbackUrl
    )
    {
        $this->contractId = $contractId;
        $this->cmsReference = $cmsReference;
        $this->productPrice = $productPrice;
        $this->subscriber = $subscriber;
        $this->callbackUrl = $callbackUrl;
        $this->callbackToken = bin2hex(openssl_random_pseudo_bytes(32));
    }

    public function getAll()
    {
        return [
            $this->contractId,
            $this->cmsReference,
            $this->productPrice,
            $this->subscriber,
            $this->callbackUrl,
            $this->callbackToken
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
     * @return string
     */
    public function getCallbackToken()
    {
        return $this->callbackToken;
    }
}
