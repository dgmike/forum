<?php

namespace App\Controller;
use App\Model;

class Blacklist
{
    public function get()
    {
        $model = new Model\Blacklist;
        $words = $model->getWords();
        $letters = $model->getLetters();
        include dirname(dirname(__FILE__)) . '/Template/Blacklist.php';
    }

    public function post()
    {
        $model = new Model\Blacklist;
        if (!empty($_POST['word'])) {
            $word = trim(filter_input(INPUT_POST, 'word', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            if ($word) {
                $model->addWord($word);
            }
        }
        if (isset($_POST['letter_in']) && isset($_POST['letter_out'])) {
            $letter_in = trim(filter_input(INPUT_POST, 'letter_in', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $letter_out = trim(filter_input(INPUT_POST, 'letter_out', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            if (strlen($letter_in) && strlen($letter_out)) {
                $model->addLetters($letter_in, $letter_out);
            }
        }
        header('Location: /blacklist');
    }
}
