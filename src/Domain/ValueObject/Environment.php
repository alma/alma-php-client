<?php

namespace Alma\API\Domain\ValueObject;

use InvalidArgumentException;

class Environment {

    const LIVE_MODE = 'live';
    const TEST_MODE = 'test';
    // Custom mode is used for testing purposes, allowing to set a custom API URL
    const CUSTOM_MODE = 'custom';

	private string $mode;

	public function __construct(string $mode) {
        if (!in_array($mode, [self::LIVE_MODE, self::TEST_MODE, self::CUSTOM_MODE])) {
            $mode = self::LIVE_MODE;
        }
		$this->mode = $mode;
	}

    public function getMode(): string {
        return $this->mode;
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
