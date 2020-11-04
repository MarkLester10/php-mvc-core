<?php

namespace marklester\phpmvc\middlewares;


abstract class BasedMiddleware
{
    abstract public function execute();
}