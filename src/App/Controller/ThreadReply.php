<?php

namespace App\Controller;
use App\Model\Message;

class ThreadReply
{
    public function get()
    {
        $data = (object) array_map(
            'intval', array_combine(
                array('thread_id', 'previous_page', 'reply_to'),
                func_get_args()
            )
        );
        $cache = \Sys\Cache::$instance;
        $cacheKey = 'thead:' . $data->thread_id
                  . ':' . $data->previous_page . ':' . $data->reply_to;
        if ($cacheOutput = $cache->get($cacheKey)) {
            echo $cacheOutput;
            return;
        }
        $model = new Message;
        $reply_to = $model->getMessage($data->reply_to);
        if (   !$reply_to 
            || ($reply_to->top_parent_id == 0 && $reply_to->id_message != $data->thread_id)
            || ($reply_to->top_parent_id != 0 && $reply_to->top_parent_id != $data->thread_id)
        ) {
            header('404 Not Found');
            die('Not Found');
        }
        $header = $model->getMessage($data->thread_id);
        ob_start();
        include dirname(dirname(__FILE__)) . '/Template/ThreadReply.php';
        $cache->set(
            $cacheKey,
            $cacheOutput = ob_get_clean(), 
            10 * $cache::MINUTE
        );
        echo $cacheOutput;
    }

    public function post()
    {
        $data = (object) array_map(
            'intval', array_combine(
                array('thread_id', 'previous_page', 'reply_to'),
                func_get_args()
            )
        );
        $model = new Message;
        $reply_to = $model->getMessage($data->reply_to);
        if (   !$reply_to 
            || ($reply_to->top_parent_id == 0 && $reply_to->id_message != $data->thread_id)
            || ($reply_to->top_parent_id != 0 && $reply_to->top_parent_id != $data->thread_id)
        ) {
            header('404 Not Found');
            die('Not Found');
        }
        $answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $model->answer($reply_to->id_message, $answer);
        if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
            header('Content-type: text/plain');
            echo 'Mensagem enviada';
            die;
        }
        header(
            'Location: /thread/' . $data->thread_id . '/' 
            . $data->previous_page
        );
    }
}
