<?php

class Test_Sys_RouterTest extends PHPUnit_Framework_TestCase
{
    public $obj = null;
    public $routes = array(
        '/' => 'App\Controller\Index',
        '/(\d+)' => 'App\Controller\Post',
    );


    public function setUp()
    {
        $this->obj = new \Sys\Router;
    }

    public function testSetRoutes()
    {
        $routes = $this->routes;
        $this->obj->setRoutes($routes);
        $this->assertEquals($routes, $this->obj->routes, '-> must set the routes');
    }

    public function testGetController()
    {
        $this->obj->setRoutes($this->routes);
        $this->assertEquals('App\Controller\Index', $this->obj->getController('/'));
        $this->assertEquals('App\Controller\Post', $this->obj->getController('/20'));
    }

    public function testGetController2()
    {
        $this->obj->setRoutes($this->routes);
        $this->assertFalse($this->obj->getController('/another/url'));
    }

    public function testRepassTheRegexp()
    {
        $this->obj->setRoutes($this->routes);
        $this->obj->getController('/20');
        $this->assertEquals(array('/20', '20'), $this->obj->matches);
    }

    public function testRun()
    {
        $mock = $this->getMock('stdClass', array('get'));
        $mock->expects($this->once())
             ->method('get');
        $this->obj->setController($mock);
        $this->obj->run('get');
    }

    public function testRunGetTheParams()
    {
        $mock = $this->getMock('stdClass', array('get'));
        $mock->expects($this->once())
             ->method('get')
             ->with($this->equalTo('post'), $this->equalTo('130'));
        $this->obj->setRoutes(array('/(post|article)/(?<id>\d+)' => 'Post'));
        $this->obj->getController('/post/130');
        $this->obj->setController($mock);
        $this->obj->run('get');
    }
}
