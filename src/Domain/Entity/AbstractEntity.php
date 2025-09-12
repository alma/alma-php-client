<?php

namespace Alma\API\Domain\Entity;

use Alma\API\Infrastructure\Exception\ParametersException;

abstract class AbstractEntity
{
    protected array $requiredFields = [];

    protected array $optionalFields = [];

    /**
     * Validate mandatory fields in the provided data array and return the value.
     * @param array $data
     * @param bool $autoAssign
     * @throws ParametersException
     */
    public function __construct(array $data, bool $autoAssign = true)
    {
        $this->prepareValues($data, $autoAssign);
    }

    /**
     * @param array $data
     * @param bool $autoAssign
     * @return array
     * @throws ParametersException
     */
    protected function prepareValues(array $data, bool $autoAssign = true): array
    {
        return array_merge(
            $this->extractValues($data, $this->requiredFields, true, $autoAssign),
            $this->extractValues($data, $this->optionalFields, false, $autoAssign)
        );
    }

    /**
     * @param array $data
     * @param array $mapping
     * @param bool $required
     * @param bool $autoAssign
     * @return array
     * @throws ParametersException
     */
    protected function extractValues(array $data, array $mapping, bool $required = false, bool $autoAssign = true): array
    {
        $extractedValues = [];
        foreach ($mapping as $mappedKey => $key) {
            if (!array_key_exists($key, $data) || !property_exists($this, $mappedKey)) {
                if ($required) {
                    throw new ParametersException("Missing required property/field: $mappedKey/$key");
                }
                continue;
            }

            if ($autoAssign) {
                $this->$mappedKey = $data[$key];
            } else {
                $extractedValues[$key] = $data[$key];
            }
        }

        return $extractedValues;
    }
}
