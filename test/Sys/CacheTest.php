<?php

class Test_Sys_Cache extends PHPUnit_Framework_TestCase
{
    public function testClassExists()
    {
        $this->assertTrue(class_exists('\\Sys\\Cache'), '-> autoload calls the Cache class');
    }

    public function testCacheClassUseCacheConfiguration()
    {
        $mock = $this->getMock('\Sys\Cache\CacheInterface');
        $mock->expects($this->once())->method('init');
        $cache = new \Sys\Cache($mock);
    }

    public function testCacheClassCallsLikeProxy()
    {
        $uniqid = uniqid();
        $mock = $this->getMock('\Sys\Cache\CacheInterface');

        $mock->expects($this->once())->method('set');
        $mock->expects($this->once())->method('get')->will($this->returnValue($uniqid));
        $mock->expects($this->once())->method('delete');
        $mock->expects($this->once())->method('flush');

        $cache = new \Sys\Cache($mock);
        $cache->set('key', 'value');
        $this->assertEquals($uniqid, $cache->get('key'));
        $cache->delete('key');
        $cache->flush();
    }
}
