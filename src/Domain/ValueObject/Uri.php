<?php

declare(strict_types=1);

namespace Alma\API\Domain\ValueObject;

use InvalidArgumentException;

/**
 * Value Object to encapsulate and validate a URL.
 */
final class Uri
{
    private string $value;

    /**
     * Private constructor to enforce the use of the factory method.
     *
     * @param string $url URL to validate and encapsulate.
     * @throws InvalidArgumentException If the provided string is not a valid URL.
     */
    private function __construct(string $url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(
                sprintf('The provided string "%s" is not a valid URL.', $url)
            );
        }

        $this->value = $url;
    }

    /**
     * Factory method to create a Url instance from a string.
     */
    public static function fromString(string $url): self
    {
        return new self($url);
    }

    /**
     * Return the URL as a string.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Convert the URL to string when the object is used in a string context.
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Check equality with another Url object.
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * Get the path component of the URL.
     *
     * @return string|null Return the path or null if the component is absent.
     */
    public function getPath(): ?string
    {
        $path = parse_url($this->value, PHP_URL_PATH);

        return is_string($path) ? $path : null;
    }

    /**
     * Get the scheme component of the URL (e.g., http, https).
     *
     * @return string|null Return the scheme or null if the component is absent.
     */
    public function getScheme(): ?string
    {
        $scheme = parse_url($this->value, PHP_URL_SCHEME);
        return is_string($scheme) ? $scheme : null;
    }
}