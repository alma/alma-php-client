<?php
namespace Alma\API\Domain\Adapter;

interface BillingAddressAdapterInterface
{
    public function getFirstName(): string;
    public function getLastName() : string;
    public function getCompany() : string;
    public function getLine1() : string;
    public function getLine2() : string;
    public function getPostalCode() : string;
    public function getCity() : string;
    public function getStateProvince() : string;
    public function getCountry() : string;
    public function getEmail() : string;
}
