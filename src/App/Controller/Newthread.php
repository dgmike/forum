<?php

namespace App\Controller;
use \App\Model\Message as Message;
use \Sys\Cache as Cache;

class Newthread
{
    public function post()
    {
        $model = new Message;
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$message) {
            header('Location: /');
            die;
        }
        $model->newThread($message);
        $total = $model->totalThreads();
        $pages = ceil($total / Home::PER_PAGE);
        for ($i=1; $i < $pages; $i++) {
            Cache::$instance->delete('home:' . $i);
        }
        header('Location: /');
        die;
    }
}
