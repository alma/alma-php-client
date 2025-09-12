<?php

namespace Alma\API\Tests\Unit;

use Alma\API\Infrastructure\PaginatedResult;
use Alma\API\Infrastructure\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class PaginatedResultTest extends TestCase
{
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
        $paginatedResult = new PaginatedResult(
            $responseMock,
            function () use ($nextResponseMock) {
                return new PaginatedResult($nextResponseMock, null);
            }
        );

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
