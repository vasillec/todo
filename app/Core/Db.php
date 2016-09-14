<?php

namespace app\Core;

use app\Config;
use app\Exceptions\DataBaseException;

class Db
{
    use TSingletone;

    protected $dbh;

    protected function __construct()
    {
        try {
            $this->dbh = new \PDO(
                'mysql:host=' . Config::DB_HOST . ';' .
                'dbname=' . Config::DB_NAME,
                Config::DB_USERNAME,
                Config::DB_PASSWD,
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), null, $e);
        }
    }

    /**
     * Выполнеие подготовленного запроса не предусматривающего возвращаемых параметров
     * @param $sql (SQL запрос)
     * @param array $params (массив параметров для SQL запроса)
     * @return int (Возвращает количество строк, модифицированных SQL запросом)
     * @throws DataBaseException (Содержит информацию об ошибке уровня БД)
     */
    public function execute($sql, $params = [])
    {
        try {
            $sth = $this->dbh->prepare($sql);
            $sth->execute($params);
            return $sth->rowCount();
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Возвращает id последней добавленной строки
     * @return string (id последней добавленной строки, 0 в случае неудачи))
     */
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * Выполнеие подготовленного запроса с возвращаемыми данными
     * @param $sql (SQL запрос)
     * @param array $params (массив параметров для SQL запроса)
     * @param string $class (тип класса, объект(ы) которого будут возвращены в результате запроса)
     * @return array (массив, содержащий объект(ы) указанного класса)
     * @throws DataBaseException (Содержит информацию об ошибке уровня БД)
     */
    public function query($sql, $params = [], $class = '\stdClass')
    {
        try {
            $sth = $this->dbh->prepare($sql);
            $sth->execute($params);
            return $sth->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $class); //PDO::FETCH_PROPS_LATE нужен что бы игнорировало __construct
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Выполнеие подготовленного запроса с возвращаемым значением
     * @param $sql (SQL запрос)
     * @param array $params $params (массив параметров для SQL запроса)
     * @return string (данные одного столбца следующей строки результирующего набора)
     * @throws DataBaseException (Содержит информацию об ошибке уровня БД)
     */
    public function queryColumn($sql, $params = [])
    {
        try {
            $sth = $this->dbh->prepare($sql);
            $sth->execute($params);
            return $sth->fetchColumn();
        } catch (\PDOException $e) {
            throw new DataBaseException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Инициализация транзакции
     */
    public function beginTransaction()
    {
        if ($this->dbh) {
            $this->dbh->beginTransaction();
        }
    }

    /**
     * Фиксирует транзакцию
     */
    public function commit()
    {
        if ($this->dbh) {
            $this->dbh->commit();
        }
    }

    /**
     * Откат транзакции
     */
    public function rollBack()
    {
        if ($this->dbh) {
            $this->dbh->rollBack();
        }
    }
}