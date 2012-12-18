<?php

namespace App\Controller;
use Sys\Pagination as Pagination;

class Home
{
    const PER_PAGE = 10;

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
        $model = new \App\Model\Message;
        $total = $model->totalThreads();
        if ($page != 1 && ($page-1) * self::PER_PAGE > $total) {
            header('404 Not Found');
            die('Not Found');
        }
        $threads = $model->threads(($page-1) * self::PER_PAGE, self::PER_PAGE);
        $pagination = false;
        if ($total > self::PER_PAGE) {
            $pagination = new Pagination;
            $pagination->items_per_page = self::PER_PAGE;
            $pagination->base_url = '';
            $pagination->items_total = $total;
            $pagination->paginate();
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
