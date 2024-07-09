<?php

namespace Alma\API\Tests\Unit\Lib;

use Alma\API\Exceptions\AlmaException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Lib\ArrayUtils;
use PHPUnit\Framework\TestCase;

class ArrayUtilsTest extends TestCase
{
    public function setUp(): void
    {
        $this->arrayUtils = new ArrayUtils();
    }

    public function testSlugifyNotString()
    {
        $this->expectException(ParametersException::class);
        $this->arrayUtils->slugify(array());
    }

    public function testSlugifyOk()
    {
        $this->assertEquals('mon_status_1', $this->arrayUtils->slugify('Mon status 1'));
        $this->assertEquals('mon_status_1', $this->arrayUtils->slugify('Mon status - 1'));
        $this->assertEquals('mon_status_1', $this->arrayUtils->slugify('Mon status @ 1'));
        $this->assertEquals('mon_status_1', $this->arrayUtils->slugify('Mon status \ -- 1'));
        $this->assertEquals('_test', $this->arrayUtils->slugify('@ test'));
    }

    public function testSlugifyEmptyQuery()
    {
        $this->expectException(ParametersException::class);
        $this->arrayUtils->slugify('');
    }

    public function testSlugifyWrongStatus()
    {
        $this->expectException(ParametersException::class);
        $this->arrayUtils->slugify('\\ @ ---- ');
    }
}
