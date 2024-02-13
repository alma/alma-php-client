<?php

namespace Alma\API\Tests\Unit\Legacy\Lib;

use Alma\API\Exceptions\ParametersException;
use Alma\API\Lib\InsuranceValidator;
use PHPUnit\Framework\TestCase;

class InsuranceValidatorTest extends TestCase
{


    /**
     * @dataProvider checkCmsReferenceInvalidPayloadDataProvider
     * @param $invalidPayload
     * @return void
     * @throws ParametersException
     */
    public function testCheckCmsReferenceWithInvalidPayloadThrowParameterException($invalidPayload)
    {
        $this->expectException(ParametersException::class);
        $insuranceValidator = new InsuranceValidator();
        $insuranceValidator->checkCmsReference($invalidPayload);
    }

    public function checkCmsReferenceInvalidPayloadDataProvider()
    {
        return [
            'String payload' =>
                [
                    'payload' => 'mb-024, mb-048'
                ],
            'Empty string payload' =>
                [
                    'payload' => ''
                ],
            'Object payload' =>
                [
                    'payload' => new \stdClass()
                ],
            'Empty array' =>
                [
                    'payload' => []
                ],
            'Int payload' =>
                [
                    'payload' => 123
                ],
            'Bool payload' => [
                'payload' => true
            ],
            'Object payload in array' => [
                'payload' => ['1236', new \stdClass()]
            ],
        ];
    }}