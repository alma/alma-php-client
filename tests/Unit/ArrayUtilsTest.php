<?php

namespace Alma\API\Tests\Unit;

use Alma\API\Lib\ArrayUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class ArrayUtils
 */
class ArrayUtilsTest extends TestCase
{
    /**
     * Return options to test ArrayUtils::isAssocArray
     * @return array[]
     */
    public static function getTestArrays()
    {
        return [
            [['a', 'b', 'c'], false],
            [["0" => 'a', "1" => 'b', "2" => 'c'], false],
            [["1" => 'a', "0" => 'b', "2" => 'c'], false],
            [["a" => 'a', "b" => 'b', "c" => 'c'], true],
        ];
    }

    /**
     * @dataProvider getTestArrays
     * @return void
     */
    public function testIsAssocArray($testArray, $expectedResult)
    {
        $result = ArrayUtils::isAssocArray($testArray);
        $this->assertEquals($expectedResult, $result);
    }
}
