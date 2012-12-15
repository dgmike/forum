<?php

class Test_Sys_AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function testAutoload()
    {
        $this->assertFalse(class_exists('FooBarFoo'), '->autoload() does not try to load classes that does not begin with Sys or App');

        $autoload = new \Sys\Autoload();
        $this->assertNull($autoload->autoload('Foo'), '->autoload() returns false if it is not able to load a class');
    }
}
