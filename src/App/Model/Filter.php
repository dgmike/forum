<?php

namespace App\Model;

class Filter
{
    static public function replace($sentense)
    {
        $sentense = preg_replace_callback('/bucet(@|a)/i', create_function('$matches', 'return str_repeat("x", strlen($matches[0]));'), $sentense);
        $sentense = preg_replace_callback('/c(@|a)r(@|a)lh(0|ó|o)/i', create_function('$matches', 'return str_repeat("x", strlen($matches[0]));'), $sentense);
        return $sentense;
    }
}