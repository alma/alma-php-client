<?php

namespace Alma\Client\Application\DTO;

use InvalidArgumentException;

class RequestedProductDto implements DtoInterface {
    private int $installmentsCount;
    private ?int $deferredDays = null;

    public function __construct(int $installmentsCount) {
        if ($installmentsCount < 1) {
            throw new InvalidArgumentException("Installments count must be positive.");
        }
        $this->installmentsCount = $installmentsCount;
    }

    public function setDeferredDays(int $days): self {
        if ($days < 0) {
            throw new InvalidArgumentException("Deferred days cannot be negative.");
        }
        $this->deferredDays = $days;
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
            'installments_count' => $this->installmentsCount,
            'deferred_days'      => $this->deferredDays,
        ], function($value) {
            return $value !== null;
        });
    }
}
