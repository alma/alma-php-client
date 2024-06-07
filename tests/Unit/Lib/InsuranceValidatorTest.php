<?php

namespace Alma\API\Tests\Unit\Lib;

use Alma\API\Exceptions\ParametersException;
use Alma\API\Lib\InsuranceValidator;
use PHPUnit\Framework\TestCase;
use stdClass;

class InsuranceValidatorTest extends TestCase
{


    /**
     * @var InsuranceValidator
     */
    protected $insuranceValidator;

    public function setUp(): void
    {
        $this->insuranceValidator = new InsuranceValidator();
    }

    /**
     * @dataProvider checkCmsReferenceInvalidPayloadDataProvider
     * @param $invalidPayload
     * @return void
     * @throws ParametersException
     */
    public function testCheckCmsReferenceWithInvalidPayloadThrowParameterException($invalidPayload)
    {
        $this->expectException(ParametersException::class);
        $this->insuranceValidator->checkCmsReference($invalidPayload);
    }

    /**
     * @throws ParametersException
     */
    public function testCheckCmsReferenceWithValidPayload()
    {
        $this->assertNull($this->insuranceValidator->checkCmsReference(['123', '456']));
    }
    
    /**
     * @return array
     */
    public static function checkCmsReferenceInvalidPayloadDataProvider()
    {
        return [
            'String payload' =>
                [
                    'invalidPayload' => 'mb-024, mb-048'
                ],
            'Empty string payload' =>
                [
                    'invalidPayload' => ''
                ],
            'Object payload' =>
                [
                    'invalidPayload' => new stdClass()
                ],
            'Empty array' =>
                [
                    'invalidPayload' => []
                ],
            'Int payload' =>
                [
                    'invalidPayload' => 123
                ],
            'Bool payload' => [
                'invalidPayload' => true
            ],
            'Object payload in array' => [
                'invalidPayload' => ['1236', new stdClass()]
            ],
        ];
    }
}
