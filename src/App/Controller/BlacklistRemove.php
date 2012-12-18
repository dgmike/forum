<?php

namespace App\Controller;
use App\Model;

class BlacklistRemove
{
    public function post()
    {
        $model = new Model\Blacklist;
        if (!empty($_POST['word'])) {
            $word = trim(filter_input(INPUT_POST, 'word', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            if ($word) {
                $model->removeWord($word);
            }
        }
        if (isset($_POST['letter_in']) && isset($_POST['letter_out'])) {
            $letter_in = trim(filter_input(INPUT_POST, 'letter_in', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $letter_out = trim(filter_input(INPUT_POST, 'letter_out', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            if (strlen($letter_in) && strlen($letter_out)) {
                $model->removeLetters($letter_in, $letter_out);
            }
        }
        header('Location: /blacklist');
    }
}
