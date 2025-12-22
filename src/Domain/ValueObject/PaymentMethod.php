<?php

namespace Alma\API\Domain\ValueObject;

/**
 * Class representing different payment methods.
 * This can be used for defining payment options in the system.
 *
 * It could be moved as a real Value Object if needed.
 */
final class PaymentMethod
{
    public const PAY_LATER = 'paylater';
    public const PAY_NOW = 'paynow';
    public const CREDIT = 'credit';
    public const PNX = 'pnx';
}
