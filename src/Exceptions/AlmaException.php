<?php
/**
 * AlmaException.
 * @author    Alma <contact@almapay.com>
 * @since 2.0.0
 * @package alma/alma-php-client
 * @namespace Alma\API\Exceptions
 */

namespace Alma\API\Exceptions;

use Exception;

abstract class AlmaException extends Exception
{
    public function __construct($logger, $message = "", $code = 0, $previous = null, $logTrace = false)
    {
        $context = array(
            'message' => $this->getMessage(),
            'trace' => $this->getTrace()
        );

        if (isset($previous)) {
            $logger->error( $previous->getMessage());
        }
        if($logTrace) {
            $logger->error($message, $context);
        } else {
            $logger->error($message);
        }

        parent::__construct($message, $code, $previous);
    }
}
