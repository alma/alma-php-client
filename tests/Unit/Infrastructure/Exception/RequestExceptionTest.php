<?php

namespace Alma\API\Tests\Unit\Infrastructure\Exception;

use Alma\API\Infrastructure\Exception\RequestException;
use Alma\API\Infrastructure\ResponseInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class RequestExceptionTest extends TestCase
{
    public static function errorMessageHandlesNullResponseProvider(): array
    {
        return [
            'null_response_with_message' => [
                'Error occurred',
                null,
                null,
                'Error occurred'
            ]
        ];
    }

    /**
     * Ensure getErrorMessage handles null response scenarios
     * @dataProvider errorMessageHandlesNullResponseProvider
     * @param string $message
     * @param RequestInterface|null $request
     * @param ResponseInterface|null $response
     * @param string $expected
     * @return void
     */
    public function testErrorMessageHandlesNullResponse(string $message, ?RequestInterface $request, ?ResponseInterface $response, string $expected)
    {
        $exception = new RequestException($message, $request, $response);
        $this->assertSame($expected, $exception->getErrorMessage());
    }

    public static function errorMessageHandlesResponseWithReasonPhraseProvider(): array
    {
        return [
            'response_with_reason_phrase' => [
                '',
                null,
                Mockery::mock(ResponseInterface::class)->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error')->getMock(),
                'Internal Server Error'
            ],
            'response_with_empty_reason_phrase' => [
                '',
                null,
                Mockery::mock(ResponseInterface::class)->shouldReceive('getReasonPhrase')->andReturn('')->getMock(),
                ''
            ],
        ];
    }

    /**
     * Ensure getErrorMessage handles response with reason phrase scenarios
     * @dataProvider errorMessageHandlesResponseWithReasonPhraseProvider
     * @param string $message
     * @param RequestInterface|null $request
     * @param ResponseInterface|null $response
     * @param string $expected
     * @return void
     */
    public function testErrorMessageHandlesResponseWithReasonPhrase(string $message, ?RequestInterface $request, ?ResponseInterface $response, string $expected)
    {
        $exception = new RequestException($message, $request, $response);
        $this->assertSame($expected, $exception->getErrorMessage());
    }
}
