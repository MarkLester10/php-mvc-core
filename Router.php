<?php

namespace marklester\phpmvc;

use marklester\phpmvc\exception\NotFoundException;

class Router
{

  public Request $request;
  public Response $response;
  protected array $routes = [];

  public function __construct(Request $request, Response $response)
  {
    $this->request = $request;
    $this->response = $response;
  }

  // HTTP Methods

  public function get($path, $callback)
  {
    $this->routes['get'][$path] = $callback;
  }

  public function post($path, $callback)
  {
    $this->routes['post'][$path] = $callback;
  }



  //URL Handlers

  public function resolve()
  {

    $path = $this->request->getPath();
    $method = $this->request->method();
    $callback = $this->routes[$method][$path] ?? false;

    if ($callback === false) {
      $this->response->setStatusCode(404);
      throw new NotFoundException();
    }

    if (is_string($callback)) {
      return Application::$app->view->renderView($callback);
    }

    if (is_array($callback)) {
      /** @var \marklester\phpmvc\Controller $controller */
      //creating instance of a class
      $controller = new $callback[0]();
      Application::$app->controller = $controller;
      $controller->action = $callback[1];
      $callback[0] = $controller;

      foreach ($controller->getMiddlewares() as $middleware) {
        $middleware->execute();
      }
    }
    //execute a callback if a not a string
    return call_user_func($callback, $this->request, $this->response);
  }
}
