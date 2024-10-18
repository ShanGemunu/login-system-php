<?php

namespace app\core;

use app\core\Log;

class Request
{
    /** 
     *    get uri of current request
     *    @param 
     *    @return string   
     */
    public function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            Log::logInfo("Request","getPath","request comming to path which does not contain '?' ","success",$path);

            return $path;
        }
        $log_path = substr($path, 0, $position);
        Log::logInfo("Request","getPath","request comming to path which does contain '?' ","success",$log_path);

        return substr($path, 0, $position);
    }

    /** 
     *    get method of current request
     *    @param
     *    @return string   
     */
    public function getMethod(): string
    {
        $log_request_method = strtolower($_SERVER['REQUEST_METHOD']);
        Log::logInfo("Request","getMethod","returning method of incoming request","success",$log_request_method);

        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /** 
     *    get body of current request
     *    @param
     *    @return array   
     */
    public function getBody(): array
    {
        $body = [];
        // get, post, cookie
        $body['request'] = $_REQUEST;
        $body['cookie'] = $_COOKIE;
        $body['files'] = $_FILES;
        $body['server'] = $_SERVER;
        // json or row data
        $body['raw'] = file_get_contents('php://input');
        Log::logInfo("Request","getBody","returning body of incoming request","success","no data");

        return $body;
    }

    /** 
     *    validate request data intended to be send as json
     *    @param $requiedPrameters  ex: ["keyword","order","limit",...]
     *    @return array   
     */
    protected function jsonInputValidate(array $requiredPrameters): array
    {
        $validationStatus = ['isValidated' => false, 'invalidReason' => null];
        $request = $this->getBody();

        // check if json is send and it should not be empty by try to covert request to array
        if (!(is_array(json_decode($request['raw'], true)))) {
            $validationStatus['invalidReason'] = "empty or invalid request send";
            Log::logInfo("Request","jsonInputValidate","invalid request send","failed",$validationStatus['invalidReason']);

            return $validationStatus;
        }
        // check if json containing required fields
        $requestParameters = json_decode($request['raw'], true);
        $requiedPrametersFlipped = array_flip($requiredPrameters);
        $missingKeys = array_diff_key($requiedPrametersFlipped, $requestParameters);
        if (0 < count($missingKeys)) {
            $validationStatus['invalidReason'] = "invalid request: " . implode(",", array_keys($missingKeys)) . " parameter(s) are needed.";
            Log::logInfo("Request","jsonInputValidate","invalid request send","failed",$validationStatus['invalidReason']);

            return $validationStatus;
        }
        // check number of fields
        if (!(count($requestParameters) === count($requiredPrameters))) {
            $validationStatus['invalidReason'] = "invalid request: exceed provided fields";
            Log::logInfo("Request","jsonInputValidate","invalid request send","failed",$validationStatus['invalidReason']);

            return $validationStatus;
        }
        $validationStatus['isValidated'] = true;
        Log::logInfo("Request","jsonInputValidate","request is valid data fromat","success","no data");

        return $validationStatus;
    }

    /** 
     *    validate request data intended to be send as form 
     *    @param $requiedPrameters  ex: ["keyword","order","limit",...]
     *    @return array   
     */
    protected function formInputValidate(array $requiredPrameters): array
    {
        $validationStatus = ['isValidated' => false, 'invalidReason' => null];
        $request = $this->getBody();

        // check if form is send and it should not be empty by try to covert request to array
        if (!is_array($request['request'])) {
            $validationStatus['invalidReason'] = "empty request send";
            Log::logInfo("Request","formInputValidate","invalid request send","failed","empty request send");

            return $validationStatus;
        }
        // check if json containing required fields
        $requestParameters = $request['request'];
        $requiedPrametersFlipped = array_flip($requiredPrameters);
        $missingKeys = array_diff_key($requiedPrametersFlipped, $requestParameters);
        if (0 < count($missingKeys)) {
            $validationStatus['invalidReason'] = "invalid request: " . implode(",", array_keys($missingKeys)) . " parameters are needed.";
            Log::logInfo("Request","formInputValidate","invalid request send","failed",$validationStatus['invalidReason']);

            return $validationStatus;
        }
        // check number of fields
        if (!(count($requestParameters) === count($requiredPrameters))) {
            $validationStatus['invalidReason'] = "invalid request: exceed provided fields";
            Log::logInfo("Request","formInputValidate","invalid request send","failed",$validationStatus['invalidReason']);

            return $validationStatus;
        }
        $validationStatus['isValidated'] = true;
        Log::logInfo("Request","jsonInputValidate","request is valid","success","no data");

        return $validationStatus;
    }

    /** 
     *    get request parameters to load data for data tables. if parameters are not provided, set default values to parameters 
     *    @param $parameters  ex: ['parameter_01'=>"default value",'parameter_02'=>"default value",...]
     *    @return array   
     */
    protected function getPrametersToLoadDataForDataTables(array $defaultParameters){
        $reqest = $this->getBody();
        $draw = $reqest['request']['draw'] ?? $defaultParameters['draw'];
        $start = $reqest['request']['start'] ?? $defaultParameters['start'];
        $length = $reqest['request']['length'] ?? $defaultParameters['length'];
        $searchValue = $reqest['request']['search']['value'] ?? $defaultParameters['searchValue'];
        $orderColumnIndex = $reqest['request']['order'][0]['column'] ?? $defaultParameters['orderColumnIndex'];
        $orderDir = $reqest['request']['order'][0]['dir'] ?? $defaultParameters['orderDir'];
        $logData = implode(" , ", $defaultParameters);
        Log::logInfo("Request","getPrametersToLoadDataForDataTables","get request parameters to load data for data tables. if parameters are not provided, set default values to parameters ","success",$logData);
        $requestPrameters = [
            'request' => $reqest,
            'draw' => $draw,
            'start' => $start,
            'length' => $length,
            'searchValue' => "%$searchValue%",
            'orderColumnIndex' => $orderColumnIndex,
            'orderDir' => $orderDir
        ];

        return $requestPrameters;
    }
}