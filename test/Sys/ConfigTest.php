<?php

class Test_Sys_ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $file = '/tmp/' . uniqid();
        \Sys\Config::setFile($file);
        $this->assertEquals($file, \Sys\Config::$file);

        \Sys\Config::setAmbiance('animation');
        $this->assertEquals('animation', \Sys\Config::$ambiance);
    }

    public function testParseIni()
    {
        $temp = tempnam(sys_get_temp_dir(), uniqid());
        file_put_contents($temp, "[animation]\nname=Michael\nage=27");
        \Sys\Config::setFile($temp);
        \Sys\Config::parse();
        $this->assertEquals(
            array(
                'animation' => array(
                    'name' => 'Michael',
                    'age' => '27',
                )
            ),
            \Sys\Config::$config,
            '-> The config parses the ini file'
        );
    }

    public function testParseOnGet()
    {
        $temp = tempnam(sys_get_temp_dir(), uniqid());
        file_put_contents($temp, "[animation]\nname=Michael\nage=27");
        \Sys\Config::setFile($temp);
        \Sys\Config::$config = null;
        \Sys\Config::get('any_key');
        $this->assertEquals(
            array(
                'animation' => array(
                    'name' => 'Michael',
                    'age' => '27',
                )
            ),
            \Sys\Config::$config,
            '-> The config parses the ini file'
        );
    }

    public function testGet()
    {
        \Sys\Config::$config = array(
            'swiming' => array(
                'name' => 23,
            ),
        );
        \Sys\Config::setAmbiance('swiming');
        $this->assertEquals(23, \Sys\Config::get('name'));
    }

    public function testGetFalse()
    {
        \Sys\Config::$config = array(
            'swiming' => array(
                'name' => 23,
            ),
        );
        \Sys\Config::setAmbiance('swiming_pool');
        $this->assertFalse(\Sys\Config::get('whatever'), '-> the ambiance do not exists');
        \Sys\Config::setAmbiance('swiming');
        $this->assertFalse(\Sys\Config::get('whatever'), '-> the key do not exists');
    }
}
