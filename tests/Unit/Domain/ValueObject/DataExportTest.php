<?php

namespace Alma\API\Tests\Unit\Domain\ValueObject;

use Alma\API\Domain\Entity\DataExport;
use Alma\API\Domain\ValueObject\Environment;
use Alma\API\Domain\ValueObject\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataExportTest extends TestCase
{
    public function testIsCompleteReturnsTrueWhenExportIsComplete()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('complete');
        $property->setAccessible(true);
        $property->setValue($dataExport, true);

        $this->assertTrue($dataExport->isComplete());
    }

    public function testIsCompleteReturnsFalseWhenExportIsNotComplete()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('complete');
        $property->setAccessible(true);
        $property->setValue($dataExport, false);

        $this->assertFalse($dataExport->isComplete());
    }

    public function testGetCreatedReturnsCorrectTimestamp()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('created');
        $property->setAccessible(true);
        $property->setValue($dataExport, 1633024800);

        $this->assertEquals(1633024800, $dataExport->getCreated());
    }

    public function testGetCsvUrlReturnsCorrectUrl()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('csvUrl');
        $property->setAccessible(true);
        $property->setValue($dataExport, 'https://example.com/export.csv');

        $this->assertEquals('https://example.com/export.csv', $dataExport->getCsvUrl());
    }

    public function testGetTypeReturnsCorrectType()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('type');
        $property->setAccessible(true);
        $property->setValue($dataExport, 'payments');

        $this->assertEquals('payments', $dataExport->getType());
    }

    public function testGetUpdatedReturnsCorrectTimestamp()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('updated');
        $property->setAccessible(true);
        $property->setValue($dataExport, 1633025800);

        $this->assertEquals(1633025800, $dataExport->getUpdated());
    }

    public function testGetEndReturnsCorrectTimestamp()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('end');
        $property->setAccessible(true);
        $property->setValue($dataExport, 1633024800);

        $this->assertEquals(1633024800, $dataExport->getEnd());
    }


    public function testGetStartReturnsCorrectTimestamp()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('start');
        $property->setAccessible(true);
        $property->setValue($dataExport, 1633024800);

        $this->assertEquals(1633024800, $dataExport->getStart());
    }
}
