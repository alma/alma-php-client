<?php

namespace Alma\API\Domain\Helper;

interface IpnHelperInterface
{
    public function parameterError( string $customMessage = 'Payment validation error: no ID provided.' ):void;
    public function potentialFraudError( string $customMessage = 'Potential fraud detected.' ): void;

    public function signatureNotExistError(string $customMessage = 'Header key X-Alma-Signature does not exist.'): void;

    public function unauthorizedError(string $customMessage = 'Unauthorized request.'): void;

    public function success(): void;
}
