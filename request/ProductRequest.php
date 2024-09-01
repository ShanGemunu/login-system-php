<?php

namespace app\request;

use app\core\Request;
use app\logs\Log;

class ProductRequest extends Request
{
    /** 
     *    validate input data send for insert products by infile
     *    @param
     *    @return array   
     */
    function validateInsertProductsByInFile() : array
    {
        $validationStatus = ['isValidated' => false, 'invalidReason' => null];
        $request = $this->getBody();
        if (!isset($request['files']['products'])) {
            $validationStatus['invalidReason'] = "no file uploaded!";
            Log::logInfo("executing validateInsertProductsByInFile at ProductRequest and return invalid status as : no file uploaded!");

            return $validationStatus;
        }
        if (!($request['files']['products']['name'] === "products.csv")) {
            $validationStatus['invalidReason'] = "file format is invalid!";
            Log::logInfo("executing validateInsertProductsByInFile at ProductRequest and return invalid status as : file format is invalid!");

            return $validationStatus;
        }
        // check file size in bytes
        if (10000000 < $request['files']['products']['size']) {
            $validationStatus['invalidReason'] = "max file size uploaded exceed!";
            Log::logInfo("executing validateInsertProductsByInFile at ProductRequest and return invalid status as : max file size uploaded exceed!");

            return $validationStatus;
        }
        $validationStatus['isValidated'] = true;
        Log::logInfo("executing validateInsertProductsByInFile at ProductRequest and return status as valid");

        return $validationStatus;
    }

    /** 
     *    retrun array containes all the data of upoaded products file
     *    @param
     *    @return array   
     */
    function getBulkProductFile() : array
    {
        $request = $this->getBody();
        Log::logInfo("executing getBulkProductFile at ProductRequest and return file containing products");

        return $request['files']['products'];
    }

    /** 
     *    validate request parameters to get products by limit
     *    @param
     *    @return array   
     */
    function validateRequestParametersToGetProductsByLimit() : array
    {
        $requiredPrameters = ["keyword", "order", "limit", "offset", "column"];
        Log::logInfo("executing validateRequestParametersToGetProductsByLimit at ProductRequest");

        return $this->jsonInputValidation($requiredPrameters);
    }

    /** 
     *    get request parameters to send products by limit  
     *    @param
     *    @return array   
     */
    function getRequestParametersToLoadProductsByLimit() : array
    {
        $request = $this->getBody();
        Log::logInfo("executing getRequestParametersToLoadProductsByLimit at ProductRequest");

        return json_decode($request['raw'], true);
    }
}