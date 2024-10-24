<?php

namespace app\request;

use app\core\Request;
use app\core\Log;

class OrderRequest extends Request
{
     /** 
     *    get request parameters. if parameters are not valid, set default values to parameters to get orders by limit
     *    @param
     *    @return array   
     */
    function getParametersToGetOrdersByLimit(): array
    {
        $defaultPrameters = [
            'draw' => 0,
            'start' => 0,
            'length' => 10,
            'searchValue' => "",
            'orderColumnIndex' => 0,
            'orderDir' => "ASC"
        ];
        Log::logInfo("OrderRequest","getParametersToGetOrdersByLimit","get request parameters. if parameters are not valid, set default values to parameters to get orders by limit","success","no data");

        return $this->getPrametersToLoadDataForDataTables($defaultPrameters);
    }
}