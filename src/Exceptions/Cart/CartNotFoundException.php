<?php
/**
 * CartNotFoundException.
 * @author    Alma <contact@almapay.com>
 * @since 2.0.0
 * @package alma/alma-php-client
 * @namespace Alma\API\Exceptions\Cart
 */

namespace Alma\API\Exceptions\Cart;

use Alma\API\Exceptions\InvalidArgumentException;
use Alma\API\LogMessage;

final class CartNotFoundException extends InvalidArgumentException
{

    public function __construct($logger, $params = array(), $code = 0, $previous = null, $logTrace = false)
    {
        $message = LogMessage::getMessage(LogMessage::CART_NOT_FOUND, $params);

        parent::__construct($logger, $message, $code, $previous, $logTrace);
    }

}
