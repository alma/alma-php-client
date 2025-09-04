<?php

namespace Alma\API\DTO\MerchantBusinessEvent;

use Alma\API\Exception\ParametersException;

class CartInitiatedBusinessEventDto
{
    private const EVENT_TYPE = 'cart_initiated';

    /**
     * @var string
     */
    private string $cartId;

    /**
     * @param string $cartId
     * @throws ParametersException
     */
    public function __construct(string $cartId)
    {
        if(empty($cartId)) {
            throw new ParametersException('CartId must be a string and cannot be empty');
        }
        $this->setCartId($cartId);
    }

    private function setCartId(string $cartId): void
    {
        $this->cartId = $cartId;
    }

    public function toArray(): array
    {
        return [
            'event_type' => self::EVENT_TYPE,
            'cart_id' => $this->cartId
        ];
    }


}
