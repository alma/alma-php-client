<?php

declare(strict_types=1);

namespace Alma\API\Domain\ValueObject;

use InvalidArgumentException;

final class Environment {

    const LIVE_MODE = 'live';
    const TEST_MODE = 'test';
    // Custom mode is used for testing purposes, allowing to set a custom API URL
    const CUSTOM_MODE = 'custom';

    const LIVE_API_URL = 'https://api.getalma.eu';
    const SANDBOX_API_URL = 'https://api.sandbox.getalma.eu';

    private string $mode;

    private Uri $baseUri;

    /**
     * Environment constructor.
     *
     * @param string $mode The environment mode: live, test, or custom
     * @param string $customApiUrl The custom API URL, required if mode is custom
     */
    public function __construct(string $mode, string $customApiUrl = '') {
        // Check mode
        if (!in_array($mode, [self::LIVE_MODE, self::TEST_MODE, self::CUSTOM_MODE])) {
            $mode = self::LIVE_MODE;
        }
        if ($mode === self::CUSTOM_MODE && empty($customApiUrl)) {
            throw new InvalidArgumentException('Custom API URL must be provided for custom mode.');
        }
        $this->mode = $mode;

        // Define Base URI based on the mode
        if ($mode === self::LIVE_MODE) {
            $this->baseUri = Uri::fromString(self::LIVE_API_URL);
        } elseif ($mode === self::TEST_MODE) {
            $this->baseUri = Uri::fromString(self::SANDBOX_API_URL);
        } else {
            $this->baseUri = Uri::fromString(rtrim($customApiUrl, '/'));
        }
    }

    public static function fromString(string $mode, string $customApiUrl = ''): self {
        return new self($mode, $customApiUrl);
    }

    public function getMode(): string {
        return $this->mode;
    }

    public function getBaseUri(): Uri {
        return $this->baseUri;
    }

    public function equals(Environment $environment): bool {
        return $this->mode === $environment->getMode();
    }

    public function isLiveMode(): bool {
        return $this->mode === self::LIVE_MODE;
    }

    public function isTestMode(): bool {
        return $this->mode === self::TEST_MODE;
    }

    public function isCustomMode(): bool {
        return $this->mode === self::CUSTOM_MODE;
    }

    public function __toString(): string {
        return $this->mode;
    }
}
