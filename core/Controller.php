<?php
namespace app\core;

use app\controllers\UserController;
use app\core\Application;
use app\models\BaseModel;
use app\core\Log;
use app\middlewares\BaseMiddleware;

class Controller
{
    const RULE_REQUIRED = 'required';
    const RULE_EMAIL = 'email';
    const RULE_MIN = 'min';
    const RULE_MAX = 'max';
    const RULE_MATCH = 'match';
    const RULE_UNIQUE = 'unique';

    protected BaseModel $model;
    protected array $middlewares = [];
    public array $errors = [];
    public string $layout = 'main';
    public string $action = "";

    /** 
     *    add errors to $errors array  
     *    @param string $attribute
     *    @param string $rule
     *    @param array $params
     *    @return void   
     */
    protected function addErrorByRule(string $attribute, string $rule, $params = [])
    {
        $log_data_param = implode(",",$params);
        Log::logInfo("Controller","addErrorByRule","adding errors by predifined rules to errors array","success","attribute - $attribute; rule - $rule; params - $log_data_param");
        $errorMessage = $this->errorMessages()[$rule];
        foreach ($params as $key => $value) {
            $errorMessage = str_replace("{{$key}}", $value, $errorMessage);
        }
        $this->errors[$attribute][] = $errorMessage;
    }

    /** 
     *    return errors from $errors array 
     *    @param
     *    @return array   
     */
    private function errorMessages()
    {
        Log::logInfo("Controller","errorMessages","return errors from errors array ","success","no data");

        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be valid email address',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be the same as {match}',
            self::RULE_UNIQUE => 'Record with with this {field} already exists',
        ];
    }

    /** 
     *    set layout for specific controller
     *    @param string $layout
     *    @return void   
     */
    public function setLayout(string $layout): void
    {
        Log::logInfo("Controller","setLayout","set layout for specific controller","success","layout - $layout");
        $this->layout = $layout;
    }

    /** 
     *    retrun specific view
     *    @param string $view
     *    @param array $params 
     *    @return string
     */
    public function render($view, array $params = [])
    {
        Log::logInfo("Controller","render","retrun specific view","success","view - $view");

        return Application::$app->router->renderView($view, $params);
    }

    /** 
     *    register middlewares 
     *    @param BaseMiddleware $middleware 
     *    @return void
     */
    public function registerMiddleware(BaseMiddleware $middleware) : void
    {
        $log_data_middleware_class = get_class($middleware);
        Log::logInfo("Controller","registerMiddleware","regiter a middleware in middlewares array","success","middleware class - $log_data_middleware_class");
        $this->middlewares[] = $middleware;
    }

   /** 
     *    get middlewares 
     *    @return array 
     */
    public function getMiddlewares(): array
    {
        Log::logInfo("Controller","registerMiddleware","get middlewares from middlewares array","success","middlewares - ...");

        return $this->middlewares;
    }

}