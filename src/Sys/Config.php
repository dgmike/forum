<?php

namespace Sys;

class Config
{
    static public $file;
    static public $ambiance;
    static public $config;

    static public function setFile($file)
    {
        self::$file = $file;
    }

    static public function setAmbiance($ambiance)
    {
        self::$ambiance = $ambiance;
    }

    static public function get($key)
    {
        if (!self::$config) {
            self::parse();
        }
        if (!isset(self::$config[self::$ambiance])) {
            return false;
        }
        if (!isset(self::$config[self::$ambiance][$key])) {
            return false;
        }
        return self::$config[self::$ambiance][$key];
    }

    static public function parse()
    {
        self::$config = parse_ini_file(self::$file, true);
    }
}
