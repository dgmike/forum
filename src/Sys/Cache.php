<?php

namespace Sys;

class Cache
{
    static $instance = null;
    protected $cacheObject;

    const MINUTE = 60;

    public function __construct(Cache\CacheInterface $instance)
    {
        self::$instance =& $this;
        $this->cacheObject = $instance;
        $args = func_get_args();
        array_shift($args);
        $this->cacheObject->init($args);
    }

    public function set($key, $value, $expire = 0)
    {
        return $this->cacheObject->set($key, $value, $expire);
    }

    public function get($key)
    {
        return $this->cacheObject->get($key);
    }

    public function delete($key)
    {
        return $this->cacheObject->delete($key);
    }

    public function flush()
    {
        return $this->cacheObject->flush();
    }
}
