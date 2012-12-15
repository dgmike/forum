<?php

namespace Sys;

class Autoload
{
    static public function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    public static function autoload($class)
    {
        if (0 !== strpos($class, 'Sys\\') || 0 !== strpos($class, 'App\\')) {
            return;
        }

        if (is_file($file = dirname(__FILE__).'/../'.str_replace(array('_', "\0"), array('/', ''), $class).'.php')) {
            require $file;
        }
    }
}
