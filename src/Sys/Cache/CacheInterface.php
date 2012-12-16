<?php

namespace Sys\Cache;

interface CacheInterface
{
    public function init();

    public function get($key);

    public function set($key, $value, $expire = 0 /* seconds */);

    public function delete($key);

    /* removes all cache */
    public function flush();
}
