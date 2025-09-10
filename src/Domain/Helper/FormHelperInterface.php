<?php

namespace Alma\API\Domain\Helper;

interface FormHelperInterface
{
    /**
     * Set a token field for form submission.
     *
     * @param string $token The token field name.
     * @param string $action The action name.
     *
     * @return string The generated token.
     */
    public function generateTokenField( string $token, string $action ): string;

    /**
     * Checks if the token form field is valid.
     *
     * @param string $token The token field name.
     * @param string $action The action name.
     *
     * @return bool True if the token is valid, false otherwise.
     */
    public function validateTokenField( string $token, string $action ): bool;
}
