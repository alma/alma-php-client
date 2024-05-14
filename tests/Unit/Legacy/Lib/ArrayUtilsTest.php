<?php

namespace Alma\API\Tests\Unit\Legacy\Lib;

use Alma\API\Exceptions\AlmaException;
use Alma\API\Lib\ArrayUtils;
use PHPUnit\Framework\TestCase;

class ArrayUtilsTest extends TestCase
{
    public function setUp()
    {
        $this->arrayUtils = new ArrayUtils();
    }

    public function testSlugifyOk()
    {
        $this->assertEquals('mon_label_1', $this->arrayUtils->slugify('Mon label 1'));
        $this->assertEquals('mon_label_1', $this->arrayUtils->slugify('Mon label - 1'));
        $this->assertEquals('mon_label_1', $this->arrayUtils->slugify('Mon label @ 1'));
        $this->assertEquals('mon_label_1', $this->arrayUtils->slugify('Mon label \ -- 1'));
        $this->assertEquals('_test', $this->arrayUtils->slugify('@ test'));
    }

    public function testSlugifyEmptyQuery()
    {
        $this->expectException(AlmaException::class);
        $this->arrayUtils->slugify('');
    }

    public function testSlugifyWrongLabel()
    {
        $this->expectException(AlmaException::class);
        $result = $this->arrayUtils->slugify('\\ @ ---- ');
    }
}
