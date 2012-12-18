<?php

namespace App\Model;

class Filter
{
    static public function replace($sentense)
    {
        $sentense = preg_replace_callback('/\bbucet(@|a)\b/i', create_function('$matches', 'return str_repeat("x", strlen($matches[0]));'), $sentense);
        $sentense = preg_replace_callback('/\bc(@|a)r(@|a)lh(0|ó|o)\b/i', create_function('$matches', 'return str_repeat("x", strlen($matches[0]));'), $sentense);
        return $sentense;
    }
}