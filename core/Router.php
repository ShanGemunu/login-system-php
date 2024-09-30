<?php

namespace app\core;

use app\core\Log;

class Router
{
    private $request;
    private $response;
    protected array $routes = [];

    /** 
     *    register an action for a controller in get 
     *    @param string $path,
     *    @param array $callback
     *    @return  void   
     */
    public function get(string $path, array $callback): void
    {
        $log_callback = implode(' ', $callback);
        Log::logInfo("Router","get","register callback for specific path of get","success","path - $path, callback - $log_callback");
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
        Log::logInfo("Router","post","register callback for specific path of post","success","path - $path, callback - $log_callback");
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
        $path = Application::$app->request->getPath();
        $method = Application::$app->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            Application::$app->response->setStatusCode(404);
            Log::logInfo("Router","resolve","when there is no callback, render not-found view","success"," path - $path; method - $method");
            Application::$app->response->setStatusCode(404);

            return $this->renderView("not-found");
        }
        if (is_string($callback)) {
            Log::logInfo("Router","resolve","when callback is string, render view","success"," path - $path; method - $method" );

            return $this->renderView($callback);
        }

        $log_data_callback_class =  $callback[0];
        $controller = new $callback[0];
        $controller->action = $callback[1];
        Application::$app->controller = $controller;
        $middlewares = $controller->getMiddlewares();
        foreach ($middlewares as $middleware) {
            $middleware->execute();
        }
        $callback[0] = Application::$app->controller;
        Log::logInfo("Router","resolve","when callback is array, call callback","success","path - $path; method - $method; class - $log_data_callback_class; function - $callback[1] ");

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
        Log::logInfo("Router","renderView","call to renderView method of view class","success","view - $view; params - ...");

        return Application::$app->view->renderView($view, $params);
    }
}