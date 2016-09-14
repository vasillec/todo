<?php

namespace app\Controllers;

use app\Components\Logger;
use app\Core\Controller;
use app\Exceptions\AppLogicException;
use app\Exceptions\ARDeleteException;
use app\Exceptions\ARSaveException;
use app\Exceptions\DataBaseException;
use app\Exceptions\InvalidParameterException;
use app\Exceptions\ProjectNotFoundException;
use app\Exceptions\SortTasksException;
use app\Exceptions\TaskNotFoundException;
use app\Models\Project;
use app\Models\Task;

class AjaxController extends Controller
{
    /**
     * Выполняется перед вызовом запрашиваемого action-a
     * @param $method (запрашиваемый action)
     * @throws AppLogicException (Нарушение логики приложения)
     */
    protected function before($method)
    {
        if (!$this->isAjax()) {
            throw new AppLogicException("Request is not XmlHttpRequest");
        } else if ('POST' != $_SERVER['REQUEST_METHOD']) {
            throw new AppLogicException("Incorrect request method: {$_SERVER['REQUEST_METHOD']}");
        } else if (!$this->isLoggedIn()) {
            throw new AppLogicException('Try not authenticated user access to private methods');
        }
    }

    /**
     * Возвращает массив проэктов пользователя или сообщение об ошибке, в виде json
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function getProjectsAction(array $params = [])
    {
        try {
            $message['projects'] = Project::findProjects($_SESSION['user']->id);
            foreach ($message['projects'] as $project) {
                $project->tasks = Task::findTasks($project->id);
            }
            $this->view->responseJson(json_encode($message));
        } catch (InvalidParameterException $e) {
            Logger::getInstance()->error($e);
            exit(json_encode(['error' => $e->getMessage() . ' Refresh the page.']));
        }
    }

    /**
     * Добавляет проект (возвращает true или сообщение об ошибке, в виде json)
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function addProjectAction(array $params = [])
    {
        try {
            $project = Project::createInstance($_POST['name'], $_SESSION['user']->id);
            $project->save();
            $message['projects'] = $project;
            $this->view->responseJson(json_encode($message));
        } catch (InvalidParameterException $e) {
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
            exit();
        } catch (ARSaveException $e) {
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Failed to add project.']));
            exit();
        }
    }

    /**
     * Удаляет проект (возвращает массив проэктов пользователя или сообщение об ошибке, в виде json)
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function deleteProjectAction(array $params = [])
    {
        try {
            $project = Project::findProject($_SESSION['user']->id, $_POST['id']);
            $project->delete();
            $this->getProjectsAction();
        } catch (InvalidParameterException $e) {
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
            exit();
        } catch (ProjectNotFoundException $e) {
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Failed to remove the project.']));
            exit();
        } catch (ARDeleteException $e) {
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Failed to remove the project.']));
            exit();
        }
    }

    /**
     * Переименовывает проект (возвращает true или сообщение об ошибке, в виде json)
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function renameProjectAction(array $params = [])
    {
        try {
            $project = Project::findProject($_SESSION['user']->id, $_POST['id']);
            $project->setName($_POST['name']);
            $project->save();
            $this->view->responseJson(json_encode(true));
        } catch (InvalidParameterException $e) {
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
            exit();
        } catch (ProjectNotFoundException $e) {
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Failed to rename the project.']));
            exit();
        } catch (ARSaveException $e) {
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Failed to rename the project.']));
            exit();
        }
    }

    /**
     * Добавляет задачу (возвращает массив проэктов пользователя или сообщение об ошибке, в виде json)
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function addTaskAction(array $params = [])
    {
        try {
            $task = Task::createInstance($_POST['name'], $_POST['project_id'], $_SESSION['user']->id);
            $task->save();
            $this->getProjectsAction();
        } catch (InvalidParameterException $e) {
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
            exit();
        } catch (ARSaveException $e) {
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Failed to add the task.']));
            exit();
        }
    }

    /**
     * Удаляет задачу (возвращает массив проэктов пользователя или сообщение об ошибке, в виде json)
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function deleteTaskAction(array $params = [])
    {
        try {
            $task = Task::findTask($_SESSION['user']->id, $_POST['id']);
            $task->delete();
            $this->getProjectsAction();
        } catch (InvalidParameterException $e) {
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
            exit();
        } catch (TaskNotFoundException $e) {
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Failed to remove the task.']));
            exit();
        } catch (ARDeleteException $e) {
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Failed to remove the task.']));
            exit();
        }
    }

    /**
     * Переименовывает задачу (возвращает true или сообщение об ошибке, в виде json)
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function renameTaskAction(array $params = [])
    {
        try {
            $task = Task::findTask($_SESSION['user']->id, $_POST['id']);
            $task->setName($_POST['name']);
            $task->save();
            $this->view->responseJson(json_encode(true));
        } catch (InvalidParameterException $e) {
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
            exit();
        } catch (TaskNotFoundException $e) {
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Failed to rename the task.']));
            exit();
        } catch (ARSaveException $e) {
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Failed to rename the task.']));
            exit();
        }
    }

    /**
     * Изменяет статус задачи (возвращает true или сообщение об ошибке, в виде json)
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function editTaskStatusAction(array $params = [])
    {
        try {
            $task = Task::findTask($_SESSION['user']->id, $_POST['id']);
            $task->changeStatus();
            $task->save();
            $this->view->responseJson(json_encode(true));
        } catch (InvalidParameterException $e) {
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
            exit();
        } catch (TaskNotFoundException $e) {
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'This event has not been processed. Refresh the page.']));
            exit();
        }
    }

    /**
     * Изменяет приоритет задач проекта (возвращает true или сообщение об ошибке, в виде json)
     * @param array $params (параметры переданные пользователем в адресной строке)
     * @throws DataBaseException (ошибка уровня БД)
     */
    protected function sortTasksAction(array $params = [])
    {
        try {
            Task::sortTasks($_SESSION['user']->id, $_POST['arr']);
            $this->view->responseJson(json_encode(true));
        } catch (InvalidParameterException $e) {
            $this->view->responseJson(json_encode(['error' => $e->getMessage()]));
            exit();
        } catch (SortTasksException $e){
            Logger::getInstance()->error($e);
            $this->view->responseJson(json_encode(['error' => 'Invalid parameters.']));
            exit();
        }
    }
}