<?php

namespace EdwinFound\Utils\Tests\Filter;

use EdwinFound\Utils\Filter\ArrayFilter;

class ArrayTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $filter = new ArrayFilter();
        $filter->add('key1');
        $filter->add('key2');
        $this->assertTrue($filter->has('key1'), 'key1 not exists');
        $this->assertTrue(!$filter->has('key3'), 'key3 not exists');
    }
}