<?php

namespace marklester\phpmvc\middlewares;

use marklester\phpmvc\Application;
use marklester\phpmvc\exception\ForbiddenException;

class AuthMiddleware extends BasedMiddleware
{
    public array $actions = [];

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if (Application::$app->isGuest()) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }
}
