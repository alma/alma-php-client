<?php

namespace Alma\API\Application\DTO;

use InvalidArgumentException;

class EligibilityQueryDto implements DtoInterface {
    private int $installmentsCount = 1;
    private int $deferredDays = 0;
    private int $deferredMonths = 0;

    public function __construct(
        int $installmentsCount
    ) {
        $this->setInstallmentsCount($installmentsCount);
    }

    public function setInstallmentsCount(int $installmentsCount): self {
        if ($installmentsCount <= 0 || $installmentsCount > 12) {
            throw new InvalidArgumentException("Installments count must be between 1 and 12.");
        }
        $this->installmentsCount = $installmentsCount;
        return $this;
    }

    public function setDeferredDays(int $deferredDays): self {
        if ($deferredDays < 0) {
            throw new InvalidArgumentException("Installments count must be positive.");
        }
        $this->deferredDays = $deferredDays;
        return $this;
    }

    public function setDeferredMonths(int $deferredMonths): self {
        if ($deferredMonths < 0) {
            throw new InvalidArgumentException("Installments count must be positive.");
        }
        $this->deferredMonths = $deferredMonths;
        return $this;
    }

    /**
     * Convert the Dto to an array.
     * This method prepares the DTO for serialization or API requests.
     *
     * @return array
     */
    public function toArray(): array {
        return array_filter([
            'installments_count'        => $this->installmentsCount,
            'deferred_days'             => $this->deferredDays,
            'deferred_months'           => $this->deferredMonths,
        ], function($value) {
            return $value !== null && $value !== '' && !(is_array($value) && empty($value));
        });
    }
}
