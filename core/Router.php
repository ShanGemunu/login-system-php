<?php

namespace app\core;

class Router
{
    private $request;
    private $response;
    protected array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /*
        register an action for a controller in get 
        @params string, callback
        @return  void
        
    */
    public function get(string $path, array $callback): void
    {
        $this->routes['get'][$path] = $callback;
    }

    /*
        register an action for a controller in post 
        @params string, callback
        @return void
        
    */
    public function post(string $path, array $callback): void
    {
        $this->routes['post'][$path] = $callback;
    }

    
    /*
        get relevent action and controller for current request and 
        call that action of controller, if not action and controller found then
        render not-found page 
        @params 
        @return string
        
    */
    public function resolve(): string
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            $this->response->setStatusCode(404);
            return $this->renderView("not-found");
        }
        if (is_string($callback)) {
            return $this->renderView($callback);
        }

        if (is_array($callback)) {
            Application::$app->controller = new $callback[0];

        }
        $callback[0] = Application::$app->controller;

        return call_user_func($callback);
    }

    
    /*
        call renderView function of view class to get view
        @params string, array
        @return string
        
    */
    public function renderView(string $view, $params = [])
    {
        return Application::$app->view->renderView($view,$params);
    }
}