<?php

class Test_App_Model_MessageTest extends PHPUnit_Framework_TestCase
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

    public function testNewConnection()
    {
        new \App\Model\Message;
    }

    public function testCountThreads()
    {
        $this->assertEquals(
            3, $this->obj->totalThreads()
        );
    }

    public function testGetThreads()
    {
        $stmt = $this->obj->threads(0, 5);
        $this->assertEquals(3, $stmt->rowCount());
        $this->assertEquals(7, $stmt->fetchObject()->id_message, '-> must get last item');
    }

    public function testGetThreads2()
    {
        $stmt = $this->obj->threads(0, 2);
        $this->assertEquals(2, $stmt->rowCount(), '-> the limit must work');
    }

    public function testGetThreads3()
    {
        $stmt = $this->obj->threads(1, 5);
        $this->assertEquals(2, $stmt->fetchObject()->id_message, '-> the skip must work');
    }

    public function testCreateNewThread()
    {
        $id = $this->obj->newThread('Já viram o novo filme do Batman?');
        $this->assertEquals(10, $id, '-> must return the last_inserted_id');
        $this->assertEquals(4, $this->obj->totalThreads(), '-> must register in database');
    }

    public function testCreateNewThread2()
    {
        $id = $this->obj->newThread('Já viram o novo filme do Batman?', \App\Model\Message::STATUS_PENDING);
        $this->assertEquals(10, $id, '-> must return the last_inserted_id');
        $this->assertEquals(3, $this->obj->totalThreads(), '-> must not register in database, because is pending');
    }

    public function testDropThread()
    {
        $this->obj->deleteThread(7);
        $this->assertEquals(2, $this->obj->totalThreads(), '-> item removed from database, status changed');
    }

    public function testDropThread2()
    {
        $this->obj->deleteThread(3);
        $result = $this->obj->query('select status from message where id_message = 3');
        $this->assertEquals(
            \App\Model\Message::STATUS_PUBLISHED, 
            $result->fetchObject()->status,
            '-> not change if is not a thread'
        );
    }

    public function testThread()
    {
        $result = $this->obj->thread(2, $start = 0, $limit = 10);
        $this->assertEquals(4, $result->rowCount(), '-> one is deleted, so it do not count');
    }

    public function testThread2()
    {
        $result = $this->obj->thread(2, 2, 2);
        $this->assertEquals(2, $result->rowCount(), '-> must call only 2');
        $this->assertEquals(5, $result->fetchObject()->id_message, '-> must start on second row');
    }

    public function testThread3()
    {
        $result = $this->obj->thread(2, 0, 20, $withHead = true);
        $this->assertEquals(2, $result->fetchObject()->id_message, '-> using the head');
    }

    public function testAnswer()
    {
        $id = $this->obj->answer(2, 'Boa, vou lá');
        $this->assertEquals(10, $id, '-> must return the new id');
        $sql = 'SELECT * FROM message WHERE id_message = ' . $id;
        $result = $this->obj->query($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $data = array(
            'id_message'       => '10',
            'top_parent_id'    => '2',
            'parent_id'        => '2',
            'depth'            => '1',
            'slug'             => '2.3.',
            'original_message' => 'Boa, vou lá',
            'message'          => 'Boa, vou lá',
            'status'           => 'published', 
            'order'            => '1',
        );
        unset($row['date_creation']);
        $this->assertEquals($data, $row);
    }

    public function testAnswer2()
    {
        $id = $this->obj->answer(6, 'Boa, vou lá');
        $this->assertEquals(10, $id, '-> must return the new id');
        $sql = 'SELECT * FROM message WHERE id_message = ' . $id;
        $result = $this->obj->query($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $data = array(
            'id_message'       => '10',
            'top_parent_id'    => '2',
            'parent_id'        => '6',
            'depth'            => '3',
            'slug'             => '2.3.2.1.',
            'original_message' => 'Boa, vou lá',
            'message'          => 'Boa, vou lá',
            'status'           => 'published', 
            'order'            => '1',
        );
        unset($row['date_creation']);
        $this->assertEquals($data, $row);
    }

    public function testAnswer3()
    {
        $this->assertFalse(
            $this->obj->answer(200, 'Qualquer coisa'),
            '-> must not insert in invalid parent'
        );
    }

    public function testAnswer4()
    {
        $this->assertFalse(
            $this->obj->answer(8, 'Qualquer coisa'),
            '-> must not insert in invalid parent'
        );
    }
}
