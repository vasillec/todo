<?php

namespace app\Core;

use app\Exceptions\ARDeleteException;
use app\Exceptions\ARSaveException;

abstract class Model
{
    const TABLE = '';

    public $id;

    /**
     * Выполняет поиск в таблице (static::TABLE) по заданному условию
     * @param array $params (ассоц. массив условий для поиска (пр.['user' => 'admin', 'password' => 'admin']))
     * @param string $orderBy (название параметра сортировки)
     * @return array $res (массив экземпляров типа [static::class])
     */
    public static function find(array $params = [], $orderBy = 'id')
    {
        $keys = array_keys($params);
        $keys = array_map(function ($value) {
            return "{$value}=:{$value}";
        }, $keys);
        $db = Db::getInstance();
        $res = $db->query(
            ' SELECT * ' .
            ' FROM ' . static::TABLE .
            ' WHERE ' . implode(' AND ', $keys) .
            ' ORDER BY ' . $orderBy,
            $params,
            static::class
        );
        return $res;
    }

    /**
     * Выполняет поиск в таблице (static::TABLE) по id
     * @param $id (id искомой строки )
     * @return static::class
     */
    public static function findById($id)
    {
        $db = Db::getInstance();
        $res = $db->query(
            ' SELECT * FROM ' . static::TABLE .
            ' WHERE id = :id LIMIT 1',
            [':id' => $id],
            static::class
        )[0];
        return $res;
    }

    /**
     * isNew() проверяет новый ли объект
     * @return bool
     */
    public function isNew()
    {
        return empty($this->id);
    }

    /**
     * save() сохраняет или изменяет запись в БД в зависимости
     * от того новый ли объект
     */
    public function save()
    {
        ($this->isNew()) ? $this->insert() : $this->update();
    }

    /**
     * Добовляет экземпляр данного класса в БД
     * @throws ARSaveException (Не удалось добавить данные в БД)
     */
    public function insert()
    {
        $columns = [];
        $values = [];
        foreach ($this as $k => $v) {
            if ('id' == $k) {
                continue;
            }
            $columns[] = $k;
            $values[':' . $k] = $v;
        }
        $sql = '
            INSERT INTO ' . static::TABLE . '
            (' . implode(',', $columns) . ') 
            VALUES
            (' . implode(',', array_keys($values)) . ')
        ';
        $db = Db::getInstance();
        $count = $db->execute($sql, $values);
        if ($count) {
            $this->id = $db->lastInsertId();
            return;
        }
        throw new ARSaveException("Unable to save object '" . static::class .
            "' in DB | SQL prepare QUERY: '$sql' | VALUES: " . json_encode($values));
    }

    /**
     * Вносит изменения в экземпляр данного класса в БД
     */
    public function update()
    {
        $columns = [];
        $values = [];
        foreach ($this as $k => $v) {
            if ('id' != $k) {
                $columns[] = $k . ' = ' . ':' . $k;
            }
            $values[':' . $k] = $v;
        }
        $sql =
            ' UPDATE ' . static::TABLE .
            ' SET ' . implode(', ', $columns) .
            ' WHERE ' . 'id= :id';
        $db = Db::getInstance();
        $db->execute($sql, $values);
    }

    /**
     * Удаляет экземпляр данного класса из БД
     * @throws ARDeleteException (Не удалось удалить данные из БД)
     */
    public function delete()
    {
        if ($this->isNew()) {
            return;
        }
        $sql = '
            DELETE FROM ' . static::TABLE .
            ' WHERE ' . 'id= :id';
        $db = Db::getInstance();
        $count = $db->execute($sql, [':id' => $this->id]);
        if ($count) {
            $this->id = '';
            return;
        }
        throw new ARDeleteException("Unable to save object '" . static::class .
            "' in DB | SQL prepare QUERY: '$sql' ID: $this->id");
    }
}