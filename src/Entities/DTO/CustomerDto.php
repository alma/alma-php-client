<?php

namespace Alma\API\Entities\DTO;

use InvalidArgumentException;

class CustomerDto {
    private bool $isBusiness = false;
    private ?string $businessIdNumber = null;
    private ?string $businessName = null;
    private ?string $firstName = null;
    private ?string $lastName = null;
    private ?string $email = null;
    private ?string $phone = null;
    private array $addresses = [];
    private ?string $created = null;
    private ?string $id = null;
    private ?string $birthDate = null;
    private bool $bankingDataCollected = false;

    public function setIsBusiness(bool $isBusiness): self {
        $this->isBusiness = $isBusiness;
        return $this;
    }

    public function setBusinessIdNumber(?string $number): self {
        $this->businessIdNumber = $number;
        return $this;
    }

    public function setBusinessName(?string $name): self {
        $this->businessName = $name;
        return $this;
    }

    public function setFirstName(string $firstName): self {
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName(string $lastName): self {
        $this->lastName = $lastName;
        return $this;
    }

    public function setEmail(string $email): self {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email.");
        }
        $this->email = $email;
        return $this;
    }

    public function setPhone(string $phone): self {
        $this->phone = $phone;
        return $this;
    }

    public function addAddress(AddressDto $address): self {
        $this->addresses[] = $address->toArray();
        return $this;
    }

    public function setCreated(string $created): self {
        $this->created = $created;
        return $this;
    }

    public function setId(string $id): self {
        $this->id = $id;
        return $this;
    }

    public function setBirthDate(string $birthDate): self {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function setBankingDataCollected(bool $collected): self {
        $this->bankingDataCollected = $collected;
        return $this;
    }

    public function toArray(): array {
        return array_filter([
            'is_business'             => $this->isBusiness,
            'business_id_number'      => $this->businessIdNumber,
            'business_name'           => $this->businessName,
            'first_name'              => $this->firstName,
            'last_name'               => $this->lastName,
            'email'                   => $this->email,
            'phone'                   => $this->phone,
            'addresses'               => $this->addresses,
            'created'                 => $this->created,
            'id'                      => $this->id,
            'birth_date'              => $this->birthDate,
            'banking_data_collected'  => $this->bankingDataCollected,
        ], function($value) {
            return $value !== null && $value !== '';
        });
    }
}
