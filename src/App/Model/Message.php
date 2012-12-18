<?php

namespace App\Model;
use \Sys\Config as Config;
use PDO;

class Message extends PDO
{
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLISHED = 'published';
    const STATUS_DELETED = 'deleted';

    private $_valid_status = array(
        self::STATUS_PENDING,
        self::STATUS_PUBLISHED,
        self::STATUS_DELETED,
    );

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

    public function totalThreads()
    {
        $stht = $this->prepare(
            'select COUNT(id_message) as c 
             from message 
             where parent_id = ? and status = ?'
        );
        $stht->execute(array(0, 'published'));
        return $stht->fetchObject()->c;
    }

    public function threads($start, $limit)
    {
        $start = (int) $start;
        $limit = (int) $limit;
        $stmt = $this->prepare(
            'SELECT id_message, message, date_creation, slug
             FROM message
             WHERE top_parent_id = ? AND status = ?
             ORDER BY id_message DESC
             LIMIT ' . $start . ',' . $limit
        );
        $stmt->execute(array(0, 'published'));
        return $stmt;
    }

    public function newThread($message, $status = self::STATUS_PUBLISHED)
    {
        $next_id = $this->query(
            sprintf(
                'SELECT COUNT(id_message) + 1 as i
                 FROM message
                 WHERE parent_id = %d
                   AND status = %s',
                0,
                PDO::quote(self::STATUS_PUBLISHED)
            )
        )->fetchObject()->i;
        $stmt = $this->prepare(
            'INSERT INTO message
             (top_parent_id, parent_id, depth, slug, original_message, message, status)
             VALUES
             (?, ?, ?, ?, ?, ?, ?)'
        );
        // @TODO put a word filter
        $normalized_message = $message;
        $stmt->execute(
            array(
                0, 0, 0, "$next_id.",
                $message,
                $normalized_message,
                $status
            )
        );
        return $this->lastInsertId();
    }

    public function deleteThread($id)
    {
        $stmt = $this->prepare(
            'UPDATE message SET status = ?
             WHERE id_message = ? AND top_parent_id = ?'
        );
        return $stmt->execute(array(self::STATUS_DELETED, (int) $id, 0));
    }

    public function totalInthread($id_thread, $use_head = false)
    {
        $sql = sprintf(
            'SELECT 
                 COUNT(id_message) as i
             FROM message
             WHERE ( top_parent_id = ? %s)
               AND status = ?',
             ($use_head ? 'OR id_message = ' . (int) $id_thread : '')
        );
        $stmt = $this->prepare($sql);
        $stmt->execute(
            array(
                (int) $id_thread, 
                self::STATUS_PUBLISHED
            )
        );
        return $stmt->fetchObject()->i;
    }

    public function thread($id_thread, $start, $limit, $use_head = false)
    {
        $sql = sprintf(
            'SELECT 
                 id_message, top_parent_id, parent_id, depth, slug,
                 original_message, message, status, date_creation
             FROM message
             WHERE ( top_parent_id = ? %s)
               AND status = ?
             ORDER BY slug ASC
             LIMIT %d, %d',
             ($use_head ? 'OR id_message = ' . (int) $id_thread : ''),
             (int) $start, (int) $limit
        );
        $stmt = $this->prepare($sql);
        $stmt->execute(
            array(
                (int) $id_thread, 
                self::STATUS_PUBLISHED
            )
        );
        return $stmt;
    }

    public function getMessage($id, $status = self::STATUS_PUBLISHED)
    {
        $sql = 'SELECT * FROM message WHERE id_message = ? AND status = ?';
        $result = $this->prepare($sql);
        $result->execute(array((int) $id, $status));
        if (!$result->rowCount()) {
            return false;
        }
        return $result->fetchObject();
    }

    public function answer($parent_id, $message, $status = self::STATUS_PUBLISHED)
    {
        $next_id = $this->query(
            sprintf(
                'SELECT COUNT(id_message) + 1 as i
                 FROM message 
                 WHERE parent_id = %d
                   AND status = %s',
                $parent_id,
                PDO::quote(self::STATUS_PUBLISHED)
            )
        )->fetchObject()->i;
        $parent = $this->getMessage($parent_id);
        if (!$parent) {
            return false;
        }
        // @TODO make a filter
        $normalized_message = $message;
        $data = array(
            (int) ($parent->top_parent_id ? $parent->top_parent_id : $parent_id),
            (int) $parent->id_message,
            (int) $parent->depth + 1,
            $parent->slug . $next_id . '.',
            $message,
            $normalized_message,
            $status
        );
        $stmt = $this->prepare(
            'INSERT INTO message(top_parent_id, parent_id, depth, slug, original_message, message, status)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $result = $stmt->execute($data);
        return $this->lastInsertId();
    }
}
