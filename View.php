<?php

namespace marklester\phpmvc;

class View
{
    public string $title = '';
    public string $subheading = '';
    public string $headerBg = '';
    //render the view
    public function renderView($view, $params = [])
    {
        //render a view inside the main layout
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        return str_replace('{{ content }}', $viewContent, $layoutContent);
    }


    protected function layoutContent()
    {
        $layout = Application::$app->layout;
        if (Application::$app->controller) {
            $layout = Application::$app->controller->layout;
        }
        //start the output caching
        ob_start();
        //output
        include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        //returns the output instead of outputting to the browser and clears the buffer
        return ob_get_clean();
    }

    protected function renderOnlyView($view, $params)
    {
        foreach ($params as $key => $value) {
            //value of variable is evaluated as its variable name
            $$key = $value;
        }

        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }
}
