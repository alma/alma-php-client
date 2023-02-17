<?php
/**
 * InvalidArgumentException.
 * @author    Alma <contact@almapay.com>
 * @since 2.0.0
 * @package alma/alma-php-client
 * @namespace Alma\API\Exceptions
 */
namespace Alma\API;

/**
 * Class LogMessage
 * @package Alma\API
 */
class LogMessage
{
    const CART_NOT_FOUND = 'cartNotFound';

    /**
     * @var
     */
    public static $messages = array(
        self::CART_NOT_FOUND => 'No cart found - [%s]'
        // 'MAGENTO 2.6 - 12345687 - No cart found - []'
    );

    /**
     * @param $key
     * @return string|void
     */
    static function getMessage($key, $params = array()) {
        if (isset(self::$messages[$key])) {
            return sprintf(self::$messages[$key], json_encode($params));
        }

        //throw UnknownException($key);

    }
}

