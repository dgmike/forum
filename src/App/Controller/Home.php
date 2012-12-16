<?php

namespace App\Controller;

class Home
{
    public function get($page = 1)
    {
        $cache = \Sys\Cache::$instance;
        $page = (int) trim($page, '/');
        if ($page < 1) {
            $page = 1;
        }
        $cacheKey = 'home:' . $page;
        if ($cacheOutput = $cache->get($cacheKey)) {
            echo $cacheOutput;
            return;
        }
        ob_start();
        include dirname(dirname(__FILE__)) . '/Template/Home.php';
        $cache->set(
            $cacheKey,
            $cacheOutput = ob_get_clean(), 
            10 * $cache::MINUTE
        );
        echo $cacheOutput;
    }
}
