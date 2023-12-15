<?php

namespace Alma\API\Tests\Unit\Legacy\Entities\Insurance;

use Alma\API\Entities\Insurance\Contract;
use PHPUnit\Framework\TestCase;


class ContractTest extends TestCase
{
    /**
     * @var Contract $contract
     */
    protected $contract;

    /**
     * @var array[] $contractData
     */
    protected $contractData;

    protected function setUp()
    {
        $this->contractData = $this->getContractData();
        $this->contract = $this->createNewContract($this->contractData);

    }

    public function testConstructObject()
    {
        $this->assertTrue(get_class($this->contract) === Contract::class);
    }

    public function testGetIdReturnId()
    {
        $this->assertEquals($this->contract->getId(), $this->contractData['id']);
    }

    public function testGetReturnName()
    {
        $this->assertEquals($this->contract->getName(), $this->contractData['name']);
    }

    public function testGetReturnProtectionDays()
    {
        $this->assertEquals($this->contract->getProtectionDays(), $this->contractData['protection_days']);
    }

    public function testGetReturnDescription()
    {
        $this->assertEquals($this->contract->getDescription(), $this->contractData['description']);
    }

    public function testGetReturnCoverArea()
    {
        $this->assertEquals($this->contract->getCoverArea(), $this->contractData['cover_area']);
    }

    public function testGetReturnCompensationArea()
    {
        $this->assertEquals($this->contract->getCompensationArea(), $this->contractData['compensation_area']);
    }

    public function testGetReturnExclusionArea()
    {
        $this->assertEquals($this->contract->getExclusionArea(), $this->contractData['exclusion_area']);
    }

    public function testGetReturnUncoveredArea()
    {
        $this->assertEquals($this->contract->getUncoveredArea(), $this->contractData['uncovered_area']);
    }

    public function testGetReturnPrice()
    {
        $this->assertEquals($this->contract->getPrice(), $this->contractData['price']);
    }

    public function testGetReturnFiles()
    {
        $this->assertEquals($this->contract->getFiles(), $this->contractData['files']);
    }

    /**
     * @dataProvider contractDataProvider
     * @return void
     */
    public function testGetProtectionDurationInYear($days, $years)
    {
        $contractData = $this->getContractData();
        $contractData['protection_days'] = $days;
        $contract = $this->createNewContract($contractData);
        $this->assertEquals($contract->getProtectionDurationInYear(), $years);
    }

    public function contractDataProvider()
    {
        return [
            'duration less than 1 year' => [
                'days' => 364,
                'years' => 0
            ],
            'duration 1 year' => [
                'days' => 365,
                'years' => 1
            ],
            'duration 1 leap year' => [
                'days' => 366,
                'years' => 1
            ],
            'duration 2 years' => [
                'days' => 730,
                'years' => 2
            ],
            'duration 3 years' => [
                'days' => 1095,
                'years' => 3
            ],
            'duration 3 years and 2 days' => [
                'days' => 1097,
                'years' => 3
            ]
        ];
    }

    private function createNewContract($contractData)
    {
       return new Contract(
            $contractData['id'],
            $contractData['name'],
            $contractData['protection_days'],
            $contractData['description'],
            $contractData['cover_area'],
            $contractData['compensation_area'],
            $contractData['exclusion_area'],
            $contractData['uncovered_area'],
            $contractData['price'],
            $contractData['files']
        );
    }

    /**
     * @return array
     */
    private function getContractData()
    {
        return [
            'id' => 'insurance_contract_3eWqr58LexAqxUAlNVKWEf',
            'name' => 'Insurance contract Name',
            'protection_days' => 365,
            'description' => 'description_value',
            'cover_area' => 'cover_area_value',
            'compensation_area' => 'compensation_area_value',
            'exclusion_area' => 'exclusion_area_value',
            'uncovered_area' => 'uncovered_area_value',
            'price' => 6000, //price in cent
            'files' => ['https://almapay.com', 'https://getalma.eu']
        ];
    }

}
