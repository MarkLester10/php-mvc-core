<?php

namespace marklester\phpmvc;

use marklester\phpmvc\db\Database;

//since application and router are in the same namespace, declaring namespace marklester\phpmvc is not needed

class Application
{
  const EVENT_BEFORE_REQUEST = 'beforeRequest';
  const EVENT_AFTER_REQUEST = 'afterRequest';

  protected array $eventListeners = [];

  //PHP 7.4 typed properties
  public static string $ROOT_DIR;
  public string $layout = 'main';
  public string $userClass;
  public Router $router;
  public Request $request;
  public Response $response;
  public Session $session;
  public ?Controller $controller = null;
  public static Application $app;
  public Database $db;
  public ?UserModel $user; //optional it might be null
  public View $view;

  public function __construct($rootPath, array $config)
  {

    $this->userClass = $config['userClass'];

    //singleton
    self::$ROOT_DIR = $rootPath;

    self::$app = $this;

    $this->request = new Request();
    $this->response = new Response();
    $this->session = new Session();
    $this->router = new Router($this->request, $this->response);
    $this->view = new View();

    $this->db = new Database($config['db']);

    $primaryValue = $this->session->get('user');
    if ($primaryValue) {
      $primaryKey = $this->userClass::primaryKey();
      $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
    } else {
      $this->user = null;
    }
  }

  public function run()
  {

    $this->triggerEvent(self::EVENT_BEFORE_REQUEST);

    try {
      echo $this->router->resolve();
    } catch (\Exception $e) {
      $this->response->setStatusCode($e->getCode());
      echo $this->view->renderView('_error', ['exception' => $e]);
    }
  }

  public function on($eventName, $callback)
  {
    $this->eventListeners[$eventName][] = $callback;
  }

  public function triggerEvent($eventName)
  {
    $callbacks = $this->eventListeners[$eventName] ?? [];
    foreach ($callbacks as $callback) {
      call_user_func($callback);
    }
  }

  public function getController()
  {
    return $this->controller;
  }
  public function setController()
  {
    return $this->controller;
  }


  public function login(UserModel $user)
  {
    $this->user = $user;
    $primaryKey = $user->primaryKey();
    $primaryValue = $user->{$primaryKey};
    $this->session->set('user', $primaryValue);
    return true;
  }

  public function isGuest()
  {
    return !self::$app->user;
  }

  public function logout()
  {
    $this->user = null;
    $this->session->remove('user');
  }
}
