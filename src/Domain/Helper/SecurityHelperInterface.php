<?php

namespace Alma\API\Domain\Helper;

interface SecurityHelperInterface
{
    /**
     * Set a token for form submission.
     *
     * @param string $action The action name.
     *
     * @return string The generated token.
     */
    public function generateToken( string $action ): string;

    /**
     * Checks if the token is valid.
     *
     * @param string $token The token field name.
     * @param string $action The action name.
     *
     * @return bool True if the token is valid, false otherwise.
     */
    public function validateToken( string $token, string $action ): bool;
}
