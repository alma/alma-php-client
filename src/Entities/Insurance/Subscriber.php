<?php

namespace Alma\API\Entities\Insurance;

class Subscriber
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $phoneNumber;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string|null
     */
    private $birthDate;

    /**
     * @var string
     */
    private $addressLine1;

    /**
     * @var string
     */
    private $addressLine2;

    /**
     * @var string
     */
    private $zipCode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $country;

    /**
     * @param string $email
     * @param string $phoneNumber
     * @param string $lastName
     * @param string $firstName
     * @param string $addressLine1
     * @param string $addressLine2
     * @param string $zipCode
     * @param string $city
     * @param string $country
     * @param string $birthDate
     */
    public function __construct(
        $email,
        $phoneNumber,
        $lastName,
        $firstName,
        $addressLine1,
        $addressLine2,
        $zipCode,
        $city,
        $country,
        $birthDate = null
    )
    {
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->birthDate = $birthDate;
        $this->addressLine1 = $addressLine1;
        $this->addressLine2 = $addressLine2;
        $this->zipCode = $zipCode;
        $this->city = $city;
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->addressLine1;
    }

    /**
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }
}
