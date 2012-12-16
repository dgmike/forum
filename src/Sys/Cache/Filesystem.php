<?php

namespace Sys\Cache;

class Filesystem implements CacheInterface
{
    public $basepath = null;

    public function init(array $args)
    {
        if (!isset($args[0]) || !is_string($args[0])) {
            throw new \DomainException('You must set the basepath');
        }
        $this->basepath = $args[0];
    }

    public function set($key, $value, $expiration = 0)
    {
        $filename = $this->basepath . DIRECTORY_SEPARATOR . md5($key);
        if ($expiration) {
            $expiration = time() + $expiration;
        }
        $expiration = (int) $expiration;
        $value = $expiration . PHP_EOL . serialize($value);
        return file_put_contents($filename, $value);
    }

    public function get($key)
    {
        $filename = $this->basepath . DIRECTORY_SEPARATOR . md5($key);
        if (!is_file($filename)) {
            return false;
        }
        $file = fopen($filename, 'r');
        $time = (int) fgets($file);
        if ($time != 0 && $time < time()) {
            $this->delete($key);
            return false;
        }
        $data = '';
        while(!feof($file)) {
            $data .= fgets($file, 4096);
        }
        return unserialize($data);
    }

    public function delete($key)
    {
        $filename = $this->basepath . DIRECTORY_SEPARATOR . md5($key);
        if (is_file($filename)) {
            return unlink($filename);
        }
        return true;
    }

    public function flush()
    {
        foreach(glob($this->basepath . '/*') as $file) {
            unlink($file);
        }
    }
}
