<?php

namespace Alma\API\Entities\DTO\MerchantBusinessEvent;

use Alma\API\Exceptions\ParametersException;

class CartInitiatedBusinessEvent extends AbstractBusinessEvent
{
    /**
     * @var string
     */
    private $cartId;

    /**
     * @param string $cartId
     * @throws ParametersException
     */
    public function __construct($cartId)
    {
        $this->eventType = 'cart_initiated';
        if(empty($cartId) || !is_string($cartId)) {
            throw new ParametersException('CartId must be a string');
        }
        $this->cartId = $cartId;
    }

    /**
     * Get Cart Id
     *
     * @return string
     */
    public function getCartId()
    {
        return $this->cartId;
    }
}
