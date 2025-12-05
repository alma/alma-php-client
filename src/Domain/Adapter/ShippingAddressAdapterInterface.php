<?php
namespace Alma\API\Domain\Adapter;

interface ShippingAddressAdapterInterface
{
    /**
     * Get the first name
     * @return string The first name
     */
    public function getFirstName(): string;

    /**
     * Get the last name
     * @return string The last name.
     */
    public function getLastName() : string;

    /**
     * Get the company
     * @return string The company.
     */
    public function getCompany() : string;

    /**
     * Get the first line of the address
     * @return string The first line of the address.
     */
    public function getLine1() : string;

    /**
     * Get the second line of the address
     * @return string The second line of the address.
     */
    public function getLine2() : string;

    /**
     * Get the postal code
     * @return string The postal code.
     */
    public function getPostalCode() : string;

    /**
     * Get the city
     * @return string The city.
     */
    public function getCity() : string;

    /**
     * Get the state/province
     * @return string The state/province.
     */
    public function getStateProvince() : string;

    /**
     * Get the country
     * @return string The country.
     */
    public function getCountry() : string;
}
