<?php

namespace Sys;

class Cache
{
    public function __construct(Cache\CacheInterface $instance)
    {
        $this->instance = $instance;
        $this->instance->init();
    }

    public function set($key, $value, $expire = 0)
    {
        $this->instance->set($key, $value, $expire);
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
