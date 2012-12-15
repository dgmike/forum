<?php

class Test_Sys_Cache extends PHPUnit_Framework_TestCase
{
    public function testClassExists()
    {
        new \Sys\Cache;
        $this->assertTrue(class_exists('\\Sys\\Cache'), '-> autoload calls the Cache class');
    }
}
