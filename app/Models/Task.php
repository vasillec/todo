<?php

namespace app\Models;

use app\Core\Db;
use app\Core\Model;
use app\Exceptions\InvalidParameterException;
use app\Exceptions\SortTasksException;
use app\Exceptions\TaskNotFoundException;

class Task extends Model
{
    const TABLE = 'tasks';

    public $id;
    public $name;
    public $status;
    public $project_id;
    public $priority;

    protected function __construct($name, $project_id, $priority)
    {
        $this->name = $name;
        $this->status = 0;
        $this->project_id = $project_id;
        $this->priority = $priority;
    }

    /**
     * Создает экземпляр данного класса
     * @param string $name (содержание задачи)
     * @param $project_id (id проекта)
     * @param $user_id (id пользователя)
     * @return Task (экземпляр данного класса)
     * @throws InvalidParameterException (переданы невалидные параметры)
     */
    public static function createInstance($name = '', $project_id, $user_id)
    {
        $name = trim(htmlspecialchars(strip_tags($name)));
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        $project_id = filter_var($project_id, FILTER_VALIDATE_INT);
        if (500 < mb_strlen($name) || empty($name) ||
            false === $user_id || $user_id <= 0 ||
            false === $project_id || $project_id <= 0
        ) {
            throw new InvalidParameterException('Character limit exceeded. Maximum amount 500.');
        }
        $priority = self::getMaxPriority($project_id, $user_id);
        return new self($name, $project_id, ++$priority);
    }

    /**
     * Изменяет имя задачи
     * @param string $name  (содержание задачи)
     * @throws InvalidParameterException (переданы невалидные параметры)
     */
    public function setName($name)
    {
        $name = trim(htmlspecialchars(strip_tags($name)));
        if (500 < mb_strlen($name) || empty($name))
            throw new InvalidParameterException('Character limit exceeded. Maximum amount 500.');
        $this->name = $name;
    }

    /**
     * Определяет максимальный приоритет существующих задач проэкта конкретного пользователя
     * @param $project_id (id проекта)
     * @param $user_id (id пользователя)
     * @return int (макс. приоритет (0 в случае отсутствия задач))
     */
    protected static function getMaxPriority($project_id, $user_id)
    {
        $db = Db::getInstance();
        $priority = $db->queryColumn('
            SELECT MAX(priority) 
            FROM tasks, projects 
            WHERE project_id = :project_id AND user_id = :user_id',
            [':project_id' => $project_id, ':user_id' => $user_id]
        );
        return $priority ? $priority : 0;
    }

    /**
     * Изменяет статус
     */
    public function changeStatus()
    {
        $this->status ? $this->status = 0 : $this->status = 1;
    }

    /**
     * Поиск задач ассоциированных с конкретным проектом
     * @param $project_id (id проекта)
     * @return array Task (массив задач)
     * @throws InvalidParameterException (переданы невалидные параметры)
     */
    public static function findTasks($project_id)
    {
        $project_id = filter_var($project_id, FILTER_VALIDATE_INT);
        if (false === $project_id || $project_id <= 0)
            throw new InvalidParameterException('Invalid parameter.');
        $res = self::find(['project_id' => $project_id], 'priority');
        return $res;
    }

    /**
     * Поиск задачи по её id ассоциированной с конкретным пользователем
     * @param $user_id (id пользователя)
     * @param $task_id (id задачи)
     * @return Task (возвращает задачу)
     * @throws InvalidParameterException (переданы невалидные параметры)
     * @throws TaskNotFoundException (задача соответствующая переданным параметрам не была найдена)
     */
    public static function findTask($user_id, $task_id)
    {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        $task_id = filter_var($task_id, FILTER_VALIDATE_INT);
        if (false === $user_id || $user_id <= 0 || false === $task_id || $task_id <= 0)
            throw new InvalidParameterException('Invalid parameters.');
        $db = Db::getInstance();
        $res = $db->query('
            SELECT tasks.id, tasks.name, tasks.status, tasks.project_id, tasks.priority
            FROM tasks, projects 
            WHERE tasks.id = :task_id 
            AND tasks.project_id = projects.id
            AND projects.user_id = :user_id',
            [':task_id' => $task_id, ':user_id' => $user_id],
            static::class
        );
        if (!$res[0]) {
            throw new TaskNotFoundException("Project not found | User id: $user_id, Task id: $task_id | ");
        }
        return $res[0];
    }

    /**
     * Сортирует задачи в соответствии с полученным массивом
     * @param $user_id (id пользователя)
     * @param $arr (массив отсортированных id задач)
     * @throws InvalidParameterException (переданы невалидные параметры)
     * @throws SortTasksException (несоответствие id задач проекта пользователя и переданного массива)
     * @throws \Exception (ошибка возникшая в процессе выполнения транзакции)
     */
    public static function sortTasks($user_id, $arr)
    {
        $arr = explode(",", $arr);
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        if (!$arr || false === $user_id || $user_id <= 0) {
            throw new InvalidParameterException('Invalid parameters.');
        }
        $arr_id_tasks = array_map(function ($id) {
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if (!$id)
                throw new InvalidParameterException('Invalid parameters.');
            return $id;
        }, $arr);
        $params = [];
        $i = 1;
        foreach ($arr_id_tasks as $id_task) {
            $params[':id' . $i++] = $id_task;
        }
        $params[':user_id'] = $user_id;
        $db = Db::getInstance();
        $sql = "
            SELECT tasks.id, tasks.name, tasks.status, tasks.project_id, tasks.priority
            FROM tasks, projects 
            WHERE tasks.id IN (" . implode(',', array_keys($params)) . ")
            AND tasks.project_id = projects.id
            AND projects.user_id = :user_id";
        $res = $db->query($sql, $params, static::class);
        if (count($res) != count($arr)) {
            throw new SortTasksException(
                "Unsuccessful data extraction in DB | SQL prepare QUERY: '$sql' | VALUES: " . json_encode($params)
            );
        }
        try {
            $db->beginTransaction();
            foreach ($res as $task) {
                $task->priority = array_search($task->id, $arr);
                $task->save();
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
}