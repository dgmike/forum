<?php

namespace App\Controller;
use App\Model;

class BlacklistRegenerate
{
    public function get()
    {
        $model = new Model\Blacklist;
        $words = $model->getWords();
        $replaces = $model->getLetterForRegex()->fetchAll();
        header('Content-type: text/plain');
        $lines = array(
        '<?php',
        '',
        'namespace App\Model;',
        '',
        'class Filter',
        '{',
        '    static public function replace($sentense)',
        '    {',
        );
        foreach($words as $word) {
            $word = $word['word'];
            foreach($replaces as $replacer) {
                $out = strtolower($replacer['letter_out']);
                $in = explode("\n", $replacer['letter_in']);
                array_map('preg_quote', $in);
                $in[] = $out;
                $in = '(' . implode('|', $in) . ')';
                $word = str_replace($out, $in, $word);
            }
            $lines[] = '        $sentense = preg_replace_callback(\'/' 
                     . $word 
                     . '/i\', create_function(\'$matches\', \'return str_repeat("x", strlen($matches[0]));\'), $sentense);';
        }
        $lines[] = '        return $sentense;';
        $lines[] = '    }';
        $lines[] = '}';
        file_put_contents(dirname(__FILE__) . '/../Model/Filter.php', implode(PHP_EOL, $lines));
        header('Location: /blacklist/regenerated');
    }
}
