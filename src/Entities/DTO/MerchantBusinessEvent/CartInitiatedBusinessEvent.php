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
    public function __construct(string $cartId)
    {
        $this->eventType = 'cart_initiated';
        if(empty($cartId)) {
            throw new ParametersException('CartId must be a string');
        }
        $this->cartId = $cartId;
    }

    /**
     * Get Cart Id
     *
     * @return string
     */
    public function getCartId(): string
    {
        return $this->cartId;
    }
}
