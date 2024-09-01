<?php

namespace app\core;

use app\logs\Log;

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
            Log::logInfo("getPath method of Request called and return $path at getPath method of Request");

            return $path;
        }
        $log_path = substr($path, 0, $position);
        Log::logInfo("getPath method of Request called and return $log_path at getPath method of Request");

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
        Log::logInfo("getMethod method of Request called and return $log_request_method at getMethod method of Request");

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
        $body['files'] = $_FILES;
        $body['server'] = $_SERVER;
        // json or row data
        $body['raw'] = file_get_contents('php://input');
        Log::logInfo("getBody method of Request called at getBody method of Request");

        return $body;
    }

    /** 
     *    validate request data intended to be send as json
     *    @param $requiedPrameters  ex: ["keyword","order","limit",...]
     *    @return array   
     */
    protected function jsonInputValidation(array $requiredPrameters) : array
    {
        $validationStatus = ['isValidated' => false, 'invalidReason' => null];
        $request = $this->getBody();
        // check if json is send and it should not be empty by try to covert request to array
        if (!(is_array(json_decode($request['raw'], true)))) {
            $validationStatus['invalidReason'] = "empty request send";
            Log::logInfo("invalid request send as {$validationStatus['invalidReason']} at jsonInputValidation of Reqest");

            return $validationStatus;
        }
        // check if json containing required fields
        $requestParameters = json_decode($request['raw'], true);
        $requiedPrametersFlipped = array_flip($requiredPrameters);
        $missingKeys = array_diff_key($requiedPrametersFlipped, $requestParameters);
        if (0 < count($missingKeys)) {
            $validationStatus['invalidReason'] = "invalid request: " . implode(",", array_keys($missingKeys)) . " parameters are needed.";
            Log::logInfo("invalid request send as {$validationStatus['invalidReason']} at jsonInputValidation of Reqest");

            return $validationStatus;
        }
        // check number of fields
        if (!(count($requestParameters) === count($requiredPrameters))) {
            $validationStatus['invalidReason'] = "invalid request: exceed provided fields";
            Log::logInfo("invalid request send as {$validationStatus['invalidReason']} at jsonInputValidation of Reqest");

            return $validationStatus;
        }
        $validationStatus['isValidated'] = true;
        Log::logInfo("request is valid at jsonInputValidation of Reqest");

        return $validationStatus;
    }
}