<?php

class Test_Sys_PaginationTest extends PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $pagination = new \Sys\Pagination;
        $this->assertEquals(10, $pagination->items_per_page, '-> use the default value');
    }
}
