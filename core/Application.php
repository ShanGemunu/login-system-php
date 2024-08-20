<?php

namespace app\core;

use app\database\Database;

class Application
{
    public Router $router;
    public Request $request;
    public Response $response;
    public static string $ROOT_DIR;
    public static Application $app;
    public Database $db;

    public Controller $controller;

    public string $layout = 'main';

    public View $view;

    public function __construct(string $rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        self::$app = $this;
        $this->db = Database::getDatabseInstance($config);
        $this->view = new View();
    }

    /*
        start point of application
        @params 
        @return void
        
    */
    public function run(): void
    {
        try{
            echo $this->router->resolve();
        }catch(\Exception $e){
            echo $this->router->renderView('server_error');
        }
    }
}