<?php

namespace Alma\API\Application\DTO;

use InvalidArgumentException;

class AddressDto implements DtoInterface {
    private ?string $firstName = null;
    private ?string $lastName = null;
    private ?string $email = null;
    private ?string $phone = null;
    private ?string $line1 = null;
    private ?string $line2 = null;
    private ?string $postalCode = null;
    private ?string $city = null;
    private ?string $countySublocality = null;
    private ?string $stateProvince = null;
    private ?string $country = null;
    private ?string $company = null;

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
            throw new InvalidArgumentException("Invalid email format.");
        }
        $this->email = $email;
        return $this;
    }

    public function setPhone(string $phone): self {
        $this->phone = $phone;
        return $this;
    }

    public function setLine1(string $line1): self {
        $this->line1 = $line1;
        return $this;
    }

    public function setLine2(?string $line2): self {
        $this->line2 = $line2;
        return $this;
    }

    public function setPostalCode(string $postalCode): self {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function setCity(string $city): self {
        $this->city = $city;
        return $this;
    }

    public function setCountySublocality(string $countySublocality): self {
        $this->countySublocality = $countySublocality;
        return $this;
    }

    public function setStateProvince($stateProvince): self {
        $this->stateProvince = $stateProvince;
        return $this;
    }

    public function setCountry(string $country): self {
        $this->country = strtoupper($country);
        return $this;
    }

    public function setCompany(?string $company): self {
        $this->company = $company;
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
            'first_name'         => $this->firstName,
            'last_name'          => $this->lastName,
            'email'              => $this->email,
            'phone'              => $this->phone,
            'line1'              => $this->line1,
            'line2'              => $this->line2,
            'postal_code'        => $this->postalCode,
            'city'               => $this->city,
            'county_sublocality' => $this->countySublocality,
            'state_province'     => $this->stateProvince,
            'country'            => $this->country,
            'company'            => $this->company,
        ], function($value) {
            return $value !== null && $value !== '' && !(is_array($value) && empty($value));
        });
    }
}
