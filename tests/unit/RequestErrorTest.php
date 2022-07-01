<?php

namespace Alma\API\Tests;

use PHPUnit\Framework\TestCase;
use Alma\API\Response;
use Alma\API\RequestError;
use Mockery;
use Psr\Log\NullLogger;

/**
 * Class RequestErrorTest
 */
class RequestErrorTest extends TestCase
{

    /**
     * Return faulty options to test ClientOptionsValidator::validateOptions
     * @return array
     */
    public function getErrorMessageProvider()
    {
        $validExpected = 'some error message';
        $validResponse = Mockery::mock(Response::class);
        $validResponse->errorMessage = $validExpected;

        $noMessageExpected = 'error message hidden in response';
        $noMessageResponse = Mockery::mock(Response::class);
        $noMessageResponse->errorMessage = null;
        $noMessageResponse->json = [
            'errors' => [
                0 => [
                    'message' => $noMessageExpected
                ]
            ]
        ];

        return [
            'valid example' => [
                null, $validResponse, $validExpected,
            ],
            'error message hidden in response' => [
                null, $noMessageResponse, $noMessageExpected,
            ]
        ];
    }

    /**
     * @dataProvider getErrorMessageProvider
     * @return void
     * @throws ParamsError
     */
    public function testGetErrorMessage($req, $res, $expected)
    {
        $requestError = new RequestError($res->errorMessage, $req, $res);

        $this->assertEquals($expected, $requestError->getErrorMessage());
    }
}
