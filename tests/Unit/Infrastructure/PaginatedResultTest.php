<?php

namespace Alma\API\Tests\Unit\Infrastructure;

use Alma\API\Infrastructure\PaginatedResult7;
use Alma\API\Infrastructure\PaginatedResult8;
use Alma\API\Infrastructure\PaginatedResultCompatibilityTrait;
use Alma\API\Infrastructure\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class PaginatedResultTest extends TestCase
{
    use PaginatedResultCompatibilityTrait;

    const FIRST_ITEMS_RESPONSE_JSON = '{
        "data": [
            {
                "comment": "comment 1",
                "id": "1"
            },
            {
                "comment": "comment 2",
                "id": "2"

            }
        ],
        "has_more": true
    }';

    const NEXT_ITEMS_RESPONSE_JSON = '{
        "data": [
            {
                "comment": "comment 3",
                "id": "3"
            },
            {
                "comment": " 4",
                "id": "4"

            }
        ]
    }';

    /**
     * Ensure we can use all features of PaginatedResult
     */
    public function testCreatePaginatedResult()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class)
            ->makePartial();
        $responseMock->shouldReceive('getJson')->andReturn(json_decode(self::FIRST_ITEMS_RESPONSE_JSON, true));
        $nextResponseMock = Mockery::mock(Response::class)
            ->makePartial();
        $nextResponseMock->shouldReceive('getJson')->andReturn(json_decode(self::NEXT_ITEMS_RESPONSE_JSON, true));

        // PaginatedResult
        if (PHP_VERSION_ID < 80000) {
            $paginatedResult = new PaginatedResult7(
                $responseMock,
                function () use ($nextResponseMock) {
                    return new PaginatedResult7($nextResponseMock, null);
                }
            );
        } else {
            $paginatedResult = new PaginatedResult8(
                $responseMock,
                function () use ($nextResponseMock) {
                    return new PaginatedResult8($nextResponseMock, null);
                }
            );
        }

        // Assertions
        $this->assertEquals(["comment" => "comment 1","id" => "1"], $paginatedResult->current());
        $this->assertEquals(0, $paginatedResult->key());
        $paginatedResult->next();
        $this->assertEquals(1, $paginatedResult->key());
        $this->assertEquals(["comment" => "comment 2","id" => "2"], $paginatedResult->current());
        $paginatedResult->rewind();
        $this->assertTrue($paginatedResult->valid());
        $this->assertEquals(["comment" => "comment 1","id" => "1"], $paginatedResult->current());
        $paginatedResult = $paginatedResult->nextPage();
        $this->assertEquals(["comment" => "comment 3","id" => "3"], $paginatedResult->current());
        $paginatedResult = $paginatedResult->nextPage();
        $paginatedResult->rewind();
        $this->assertFalse($paginatedResult->valid());
    }
}
