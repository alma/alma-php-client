<?php

namespace Alma\API\Tests\Unit;

use Alma\API\Exceptions\RequestException;
use Alma\API\Request;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Alma\API\Response;
use Mockery;

/**
 * Class RequestErrorTest
 */
class RequestErrorTest extends MockeryTestCase
{
	const ERROR_MESSAGE = 'error message hidden in response';

	/**
     * Return faulty options to test ClientOptionsValidator::validateOptions
     * @return array
     */
    public static function getErrorMessageProvider(): array
    {
        $validExpected = 'some error message';
        $validResponse = Mockery::mock(Response::class);
        $validResponse->errorMessage = $validExpected;

        $noMessageExpected = self::ERROR_MESSAGE;
        $noMessageResponse = Mockery::mock(Response::class);
        $noMessageResponse->errorMessage = self::ERROR_MESSAGE;
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
            self::ERROR_MESSAGE => [
				null, $noMessageResponse, $noMessageExpected,
            ]
        ];
    }

    /**
     * @dataProvider getErrorMessageProvider
     * @return void
     */
    public function testGetErrorMessage(?Request $req, Response $res, $expected)
    {
        $requestError = new RequestException($res->errorMessage, $req, $res);

        $this->assertEquals($expected, $requestError->getErrorMessage());
    }
}
