<?php

namespace app\Models;

use app\Core\Model;
use app\Exceptions\InvalidParameterException;
use app\Exceptions\ProjectNotFoundException;

class Project extends Model
{
    const TABLE = 'projects';

    public $id;
    public $name;
    protected $user_id;

    protected function __construct($name, $user_id)
    {
        $this->name = $name;
        $this->user_id = $user_id;
    }

    /**
     * Создает экземпляр данного класса
     * @param string $name (название проекта)
     * @param $user_id (id пользователя)
     * @return Project (экземпляр данного класса)
     * @throws InvalidParameterException (переданы невалидные параметры)
     */
    public static function createInstance($name = '', $user_id)
    {
        $name = trim(htmlspecialchars(strip_tags($name)));
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        if (26 < mb_strlen($name) || empty($name) || false === $user_id || $user_id <= 0)
            throw new InvalidParameterException('Character limit exceeded. Maximum amount 26.');
        return new self($name, $user_id);
    }

    /**
     * Изменяет название проекта
     * @param string $name (новое название проекта)
     * @throws InvalidParameterException (переданы невалидные параметры)
     */
    public function setName($name = '')
    {
        $name = trim(htmlspecialchars(strip_tags($name)));
        if (26 < mb_strlen($name) || empty($name))
            throw new InvalidParameterException('Character limit exceeded. Maximum amount 26.');
        $this->name = $name;
    }

    /**
     * Поиск проектов ассоциированных с конкретным пользователем
     * @param $user_id (id пользователя)
     * @return array Project (массив проектов)
     * @throws InvalidParameterException (переданы невалидные параметры)
     */
    public static function findProjects($user_id)
    {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        if (false === $user_id || $user_id <= 0)
            throw new InvalidParameterException('Invalid parameter.');
        $res = self::find(['user_id' => $user_id]);
        return $res;
    }

    /**
     * Поиск проекта по его id ассоциированный с конкретным пользователем
     * @param $user_id (id пользователя)
     * @param $project_id (id проекта)
     * @return Project (возвращает найденный проект)
     * @throws InvalidParameterException (переданы невалидные параметры)
     * @throws ProjectNotFoundException (Проект соответствующий переданным параметрам не был найден)
     */
    public static function findProject($user_id, $project_id)
    {
        $user_id = filter_var($user_id, FILTER_VALIDATE_INT);
        $project_id = filter_var($project_id, FILTER_VALIDATE_INT);
        if (false === $user_id || $user_id <= 0 || false === $project_id || $project_id <= 0)
            throw new InvalidParameterException('Invalid parameters.');
        $res = self::find(['user_id' => $user_id, 'id' => $project_id]);
        if (!$res[0]) {
            throw new ProjectNotFoundException("Project not found | User id: $user_id, Project id: $project_id | ");
        } 
        return $res[0];
    }
}