<?php

namespace ExampleProject;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Data provider for testExample.
     *
     * Return an array of arrays, each of which contains the parameter
     * values to be used in one invocation of the testExample test function.
     */
    public function exampleTestValues()
    {
        return [
            [4, 2, 2,],
            [9, 3, 3,],
            [56, 7, 8,],
        ];
    }

    /**
     * Test our example class. Each time this function is called, it will
     * be passed data from the data provider function idendified by the
     * dataProvider annotation.
     *
     * @dataProvider exampleTestValues
     */
    public function testExample($expected, $constructor_parameter, $value)
    {
        $example = new Example($constructor_parameter);
        $this->assertEquals($expected, $example->multiply($value));
    }
}
