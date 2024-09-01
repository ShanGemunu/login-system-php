<?php

namespace app\core;

use app\logs\Log;

class Router
{
    private $request;
    private $response;
    protected array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $log_request = get_class($request);
        $log_response = get_class($response);
        Log::logInfo("Router instance created using parameters - $log_request and $log_response objects at __construct of Router");
        $this->request = $request;
        $this->response = $response;
    }

    /** 
    *    register an action for a controller in get 
    *    @param string $path,
    *    @param array $callback
    *    @return  void   
    */
    public function get(string $path, array $callback): void
    {
        $log_callback = implode(' ', $callback);
        Log::logInfo("register callback for $path, $log_callback in get at get method of Router");
        $this->routes['get'][$path] = $callback;
    }

    /** 
    *    register an action for a controller in post 
    *    @param string $path callback $callback
    *    @param array $callback
    *    @return void   
    */
    public function post(string $path, array $callback): void
    {
        $log_callback = implode(' ', $callback);
        Log::logInfo("register callback for $path, $log_callback in post at post method of Router");
        $this->routes['post'][$path] = $callback;
    }

    
    /** 
    *    get relevent action and controller for current request and 
    *    call that action of controller, if not action and controller found then
    *    render not-found page 
    *    @param 
    *    @return string   
    */
    public function resolve(): string
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            $this->response->setStatusCode(404);
            Log::logInfo("no callback for requset, not-found page sended at resolve method of Router");
            
            return $this->renderView("not-found");
        }
        if (is_string($callback)) {
            Log::logInfo("callback is string - $callback . relevent view sended at resolve method of Router");

            return $this->renderView($callback);
        }

        if (is_array($callback)) {
            Application::$app->controller = new $callback[0];

        }
        $callback[0] = Application::$app->controller;
        Log::logInfo("callback is array and called it at resolve method of Router");

        return call_user_func($callback);
    }

    
    /** 
    *    call renderView function of view class to get view
    *    @param string $view, array $params
    *    @param array $params
    *    @return string    
    */
    public function renderView(string $view, array $params = [])
    {
        Log::logInfo("calling renderView method of Router using parameters - $view,  and called it at resolve method of Router");

        return Application::$app->view->renderView($view,$params);
    }
}