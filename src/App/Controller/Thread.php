<?php

namespace App\Controller;
use App\Model\Message;
use Sys\Pagination;

class Thread
{
    const PER_PAGE = 10;

    public function get($thread, $page = 1)
    {
        $cache = \Sys\Cache::$instance;
        $page = (int) trim($page, '/');
        $thread = $thread_id = (int) trim($thread, '/');
        if ($page < 1) {
            $page = 1;
        }
        $cacheKey = 'thead:' . $thread . ':' . $page;
        if ($cacheOutput = $cache->get($cacheKey)) {
            echo $cacheOutput;
            return;
        }
        $model = new Message;
        $total = $model->totalInthread($thread, true);
        if (!$total || ($page != 1 && ($page-1) * self::PER_PAGE > $total)) {
            header('404 Not Found');
            die('Not Found');
        }
        $header = $model->getMessage($thread);
        $threads = $model->thread(
            $thread, ($page-1) * self::PER_PAGE, self::PER_PAGE, true
        );
        $pagination = false;
        if ($total > self::PER_PAGE) {
            $pagination = new Pagination;
            $pagination->items_per_page = self::PER_PAGE;
            $pagination->base_url = '/thread/' . $thread_id;
            $pagination->items_total = $total;
            $pagination->current_page = $page;
            $pagination->paginate();
        }
        ob_start();
        include dirname(dirname(__FILE__)) . '/Template/Thread.php';
        $cache->set(
            $cacheKey,
            $cacheOutput = ob_get_clean(), 
            10 * $cache::MINUTE
        );
        echo $cacheOutput;
    }
}
