<?php

require_once dirname(__FILE__) . '/BaseTestCase.php';

class Test_App_Model_ProcedureTest extends App_Model_BaseTestCase
{
    public function simpleInsert($id_message = null)
    {
        if (null === $id_message) {
            $id_message = time();
        }
        $stmt = $this->obj->prepare('
            INSERT INTO `message` (
                `id_message`, `top_parent_id`, `parent_id`, `depth`, `order`,
                `slug`, `original_message`, `message`, `status`,
                `date_creation`
            ) VALUES (
                :id_message, :top_parent_id, :parent_id, :depth, :order,
                :slug, :original_message, :message, :status, :date_creation
            )
        ');
        $result = $stmt->execute(
            array(
                ':id_message'       => $id_message,
                ':top_parent_id'    => $id_message,
                ':parent_id'        => 0,
                ':depth'            => 0,
                ':order'            => 1,
                ':slug'             => $id_message . '.',
                ':original_message' => 'Mensagem original',
                ':message'          => 'Mensagem filtrada',
                ':status'           => App\Model\Message::STATUS_PUBLISHED,
                ':date_creation'    => date('Y-m-d H:i:s'),
            )
        );
        $this->assertTrue(
            $result, '-> Query is executed: ' . $stmt->errorInfo()
        );
        $result = $this->obj->query(
            'SELECT `id_message` FROM message ORDER BY id_message DESC limit 1'
        );
        $this->assertEquals(
            (int) $result->fetch(PDO::FETCH_OBJ)->id_message,
            (int) $id_message,
            '-> Same ID_MESSAGE'
        );
        return (int) $id_message;
    }

    public function testCall2CreateChild()
    {
        $id_message = $this->simpleInsert();
        $stmt = $this->obj->prepare(
            'CALL treeItem(:parentID, :oriMessage, :message)'
        );
        $result = $stmt->execute(
            array(
                ':parentID' => $id_message,
                ':oriMessage' => 'Resposta original',
                ':message' => 'Resposta filtrada',
            )
        );
        $this->assertTrue(
            $result, '-> Query is executed: ' . $stmt->errorInfo()
        );
        $result = $this->obj->query(
            'SELECT 
                `id_message`, `top_parent_id`, `parent_id`, `depth`, `order`,
                `slug`, `original_message`, `message`, `status`, `date_creation`
            FROM `message`
            WHERE `parent_id` = ' . $id_message
        );
        $this->assertEquals(
            $result->fetch(PDO::FETCH_ASSOC),
            array(
                'id_message'       => (string) ($id_message + 1),
                'top_parent_id'    => (string) $id_message,
                'parent_id'        => (string) $id_message,
                'depth'            => '1',
                'order'            => '2',
                'slug'             => $id_message . '.1',
                'original_message' => 'Resposta original',
                'message'          => 'Resposta filtrada',
                'status'           => App\Model\Message::STATUS_PUBLISHED,
                'date_creation'    => date('Y-m-d H:i:s', time()),
            ),
            '-> Same data'
        );
        $result = $stmt->execute(
            array(
                ':parentID'   => $id_message,
                ':oriMessage' => 'Mais uma resposta',
                ':message'    => 'Mais uma resposta',
            )
        );
        $this->assertEquals(
            $this->obj->query(
                'SELECT COUNT(*) as `c` 
                FROM `message`
                WHERE `parent_id` = ' . $id_message
            )->fetchObject()->c,
            2,
            '-> Message must have 2 child messages'
        );
        return $id_message;
    }

    public function testCallChildOfChild()
    {
        $id_message = $this->simpleInsert();
        $result = (bool) $this->obj->exec(
            'CALL treeItem(' . $id_message . ', "Resposta", "Resposta")'
        );
        $this->assertTrue($result);
        $result = (bool) $this->obj->exec(
            'CALL treeItem(' . ($id_message + 1) . ', "Resposta", "Resposta")'
        );
        $this->assertEquals(
            3,
            $this->obj->query(
                'SELECT COUNT(*) as `c`
                 FROM `message`
                 WHERE `top_parent_id` = ' . $id_message
            )->fetchObject()->c,
            '-> Thread mus have 3 iterations (self including)'
        );
        $result = $this->obj->query(
            'SELECT COUNT(*) as `c`, `depth`, `order`
             FROM `message`
             WHERE `parent_id` = ' . $id_message
        )->fetchObject();
        $this->assertEquals(
            $result->c, 1, '-> Thread mus have only one answer'
        );
        $this->assertEquals(
            $result->depth, 1, '-> It must be child of another'
        );
        $this->assertEquals($result->order, 2, '-> second place');
        $result = $this->obj->query(
            'SELECT COUNT(*) as `c`, `depth`, `order`
             FROM `message`
             WHERE `parent_id` = ' . ($id_message + 1)
        )->fetchObject();
        $this->assertEquals($result->c, 1, '-> Answer mus have one answer');
        $this->assertEquals($result->depth, 2, '-> Child of child');
        $this->assertEquals($result->order, 3, '-> third place');
    }

    public function testCallCreateNewThread()
    {
        $result = (bool) $this->obj->exec(
            'CALL treeItem(NULL, "Nova Thread", "Nova Thread")'
        );
        $this->assertTrue($result, '-> data is saved');
        $result = $this->obj->query('
            SELECT 
                `id_message`, `top_parent_id`, `parent_id`, `depth`, `order`,
                `slug`, `original_message`, `message`, `status`,
                `date_creation`
            FROM
                `message`
            ORDER BY `id_message` DESC
            LIMIT 1
        ')->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(
            $result,
            array(
                'id_message' => '10',
                'top_parent_id' => '10',
                'parent_id' => '0',
                'depth' => '1',
                'order' => '1',
                'slug' => '10.',
                'original_message' => 'Nova Thread',
                'message' => 'Nova Thread',
                'status' => 'published',
                'date_creation' => date('Y-m-d H:i:s'),
            ),
            '-> Created a new thread'
        );
    }

    public function testMultiThreads()
    {
        $id_message = $this->simpleInsert(20);
        $stmt = $this->obj->prepare('CALL treeItem(:id, :oriMessage, :message)');
        $message = 'Resposta 1';
        $stmt->bindParam(':oriMessage', $message, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id_message);
        $stmt->execute();
        $message = 'Resposta 2';
        $stmt->execute();
        print_r(
            $this->obj->query(
                'SELECT * FROM `message` WHERE `top_parent_id` = 20'
            )->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
