<?php

namespace app\request;

use app\core\Request;
use app\core\Log;

class ProductRequest extends Request
{
    /** 
     *    validate input data send for insert products by infile
     *    @param
     *    @return array   
     */
    function validateInsertProductsByInFile(): array
    {
        $validationStatus = ['isValidated' => false, 'invalidReason' => null];
        $request = $this->getBody();
        if (!isset($request['files']['products'])) {
            $validationStatus['invalidReason'] = "no file uploaded!";
            Log::logInfo("ProductRequest","validateInsertProductsByInFile","invalid request","failed",$validationStatus['invalidReason']);

            return $validationStatus;
        }
        if (!($request['files']['products']['name'] === "products.csv")) {
            $validationStatus['invalidReason'] = "file format is invalid!";
            Log::logInfo("ProductRequest","validateInsertProductsByInFile","invalid request","failed",$validationStatus['invalidReason']);

            return $validationStatus;
        }
        // check file size in bytes
        if (10000000 < $request['files']['products']['size']) {
            $validationStatus['invalidReason'] = "max file size uploaded exceed!";
            Log::logInfo("ProductRequest","validateInsertProductsByInFile","invalid request","failed",$validationStatus['invalidReason']);

            return $validationStatus;
        }
        $validationStatus['isValidated'] = true;
        Log::logInfo("ProductRequest","validateInsertProductsByInFile","request is valid","success","no data");

        return $validationStatus;
    }

    /** 
     *    retrun array contains all the data of upoaded products file
     *    @param
     *    @return array   
     */
    function getBulkProductFile(): array
    {
        $request = $this->getBody();
        Log::logInfo("ProductRequest","getBulkProductFile","retrun array containes all the data of upoaded products file","success","no data");

        return $request['files']['products'];
    }

    /** 
     *    get request parameters. if parameters are not valid, set default values to parameters to get products by limit
     *    @param
     *    @return array   
     */
    function getParametersToGetProductsByLimit(): array
    {
        $defaultPrameters = [
            'draw' => 0,
            'start' => 0,
            'length' => 10,
            'searchValue' => "",
            'orderColumnIndex' => 0,
            'orderDir' => "ASC"
        ];
        Log::logInfo("ProductRequest","getParametersToGetProductsByLimit","get request parameters. if parameters are not valid, set default values to parameters to get products by limit","success","no data");

        return $this->getPrametersToLoadDataForDataTables($defaultPrameters);
    }
}