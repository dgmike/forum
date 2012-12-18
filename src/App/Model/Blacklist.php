<?php

namespace App\Model;
use \Sys\Config as Config;
use PDO;

class Blacklist extends PDO
{
    public function __construct()
    {
        $dns  = Config::get('db_dns');
        $user = Config::get('db_user');
        $pass = Config::get('db_pass');
        parent::__construct(
            $dns, $user, $pass, 
            array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
        );
    }

    public function getWords()
    {
        return $this->query('SELECT word FROM blacklist ORDER BY word');
    }

    public function addWord($word)
    {
        $stmt = $this->prepare('INSERT INTO blacklist (word) VALUES (?)');
        $stmt->execute(array($word));
    }

    public function removeWord($word)
    {
        $stmt = $this->prepare('DELETE FROM blacklist WHERE word = ?');
        $stmt->execute(array($word));
    }

    public function getLetters()
    {
        return $this->query('SELECT letter_in, letter_out from blacklist_letter_replacer ORDER BY letter_out');
    }

    public function addLetters($letter_in, $letter_out)
    {
        $stmt = $this->prepare('INSERT INTO blacklist_letter_replacer (letter_in, letter_out) VALUES (?, ?)');
        $stmt->execute(array($letter_in, $letter_out));
    }

    public function removeLetters($letter_in, $letter_out)
    {
        $stmt = $this->prepare('DELETE FROM blacklist_letter_replacer WHERE letter_in = ? AND letter_out = ?');
        $stmt->execute(array($letter_in, $letter_out));
    }
}
