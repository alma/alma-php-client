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
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue($dataExport, true);

        $this->assertTrue($dataExport->isComplete());
    }

    public function testIsCompleteReturnsFalseWhenExportIsNotComplete()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('complete');
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue($dataExport, false);

        $this->assertFalse($dataExport->isComplete());
    }

    public function testGetCreatedReturnsCorrectTimestamp()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('created');
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue($dataExport, 1633024800);

        $this->assertEquals(1633024800, $dataExport->getCreated());
    }

    public function testGetCsvUrlReturnsCorrectUrl()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('csvUrl');
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue($dataExport, 'https://example.com/export.csv');

        $this->assertEquals('https://example.com/export.csv', $dataExport->getCsvUrl());
    }

    public function testGetTypeReturnsCorrectType()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('type');
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue($dataExport, 'payments');

        $this->assertEquals('payments', $dataExport->getType());
    }

    public function testGetUpdatedReturnsCorrectTimestamp()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('updated');
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue($dataExport, 1633025800);

        $this->assertEquals(1633025800, $dataExport->getUpdated());
    }

    public function testGetEndReturnsCorrectTimestamp()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('end');
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue($dataExport, 1633024800);

        $this->assertEquals(1633024800, $dataExport->getEnd());
    }


    public function testGetStartReturnsCorrectTimestamp()
    {
        $dataExport = new DataExport([]);
        $reflection = new \ReflectionClass($dataExport);
        $property = $reflection->getProperty('start');
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue($dataExport, 1633024800);

        $this->assertEquals(1633024800, $dataExport->getStart());
    }
}
