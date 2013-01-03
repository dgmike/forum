<?php

class App_Model_BaseTestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \Sys\Config::setFile(SRC_PATH . '/App/Config/Config.ini');
        \Sys\Config::setAmbiance('test');
        $this->obj = new \App\Model\Message;
        $sql = file_get_contents(dirname(__FILE__) . '/../../import.sql');
        $sql = explode(';', $sql);
        foreach ($sql as $query) {
            $this->obj->exec($query);
        }
        $sql  = file_get_contents(dirname(__FILE__) . '/../../procedure.sql');
        $this->obj->beginTransaction();
        $this->obj->exec('DELIMITER $$');
        $this->obj->exec('DROP PROCEDURE IF EXISTS `treeItem`');
        $this->obj->exec($sql);
        $this->obj->exec('DELIMITER ;');
        $this->obj->commit();
        unset($this->obj);
        $this->obj = new \App\Model\Message;
    }
}
