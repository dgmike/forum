<?php

namespace Sys;

class Router
{
    public $routes  = array();
    public $matches = array();
    public $controller = null;

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getController($uri)
    {
        foreach($this->routes as $route => $controller) {
            $route = '/^' . str_replace('/', '\/', $route) . '$/';
            if (preg_match($route, $uri, $this->matches)) {
                return $controller;
            }
        }
        return false;
    }

    public function run($method)
    {
        $matches = $this->matches;
        foreach ($matches as $k=>$v) {
            if (!is_int($k)) {
                unset($matches[$k]);
            }
        }
        array_shift($matches);
        if (method_exists($this->controller, $method)) {
            call_user_func_array(
                array($this->controller, $method), 
                $matches
            );
        } else {
            header('404 Not Found');
            die('Not Found');
        }
    }
}
