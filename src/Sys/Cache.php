<?php

namespace Sys;

class Cache
{
    static $instance = null;
    const MINUTE = 60;

    public function __construct(Cache\CacheInterface $instance)
    {
        self::$instance =& $this;
        $this->instance = $instance;
        $args = func_get_args();
        array_shift($args);
        $this->instance->init($args);
    }

    public function set($key, $value, $expire = 0)
    {
        return $this->instance->set($key, $value, $expire);
    }

    public function get($key)
    {
        return $this->instance->get($key);
    }

    public function delete($key)
    {
        return $this->instance->delete($key);
    }

    public function flush()
    {
        return $this->instance->flush();
    }
}
