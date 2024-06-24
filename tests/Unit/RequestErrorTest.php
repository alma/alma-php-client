<?php

namespace Alma\API\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Alma\API\Response;
use Alma\API\RequestError;
use Mockery;

/**
 * Class RequestErrorTest
 */
class RequestErrorTest extends TestCase
{
	const ERROR_MESSAGE = 'error message hidden in response';

	/**
     * Return faulty options to test ClientOptionsValidator::validateOptions
     * @return array
     */
    public static function getErrorMessageProvider()
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
                "valid example", $validResponse, $validExpected,
            ],
            self::ERROR_MESSAGE => [
				self::ERROR_MESSAGE, $noMessageResponse, $noMessageExpected,
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
