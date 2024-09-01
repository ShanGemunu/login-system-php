<?php
namespace app\core;

use app\controllers\UserController;
use app\core\Application;
use app\models\BaseModel;
use app\logs\Log;

class Controller
{
    const RULE_REQUIRED = 'required';
    const RULE_EMAIL = 'email';
    const RULE_MIN = 'min';
    const RULE_MAX = 'max';
    const RULE_MATCH = 'match';
    const RULE_UNIQUE = 'unique';

    protected BaseModel $model;

    public array $errors = [];

    public string $layout = 'main';

    /** 
    *    add errors to $errors array  
    *    @param string $attribute
    *    @param string $rule
    *    @param array $params
    *    @return void   
    */
    protected function addErrorByRule(string $attribute, string $rule, $params = [])
    {
        Log::logInfo("addErrorRule method of Controller called with parameters - $attribute, $rule,  at addErrorRule method of Controller");
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
        Log::logInfo("errorMessages method of Controller called at errorMessages method of Controller");

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
        Log::logInfo("setLayout method of Controller called using parameters - $layout at setLayout method of Controller");
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
        Log::logInfo("render method of Controller called using parameters - $view,  at render method of Controller");

        return Application::$app->router->renderView($view, $params);
    }

}