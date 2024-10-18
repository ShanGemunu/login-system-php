<?php

namespace app\request;

use app\core\Request;
use app\core\Log;

class CartRequest extends Request
{
    /** 
     *    validate product id send from request 
     *    @param
     *    @return array   
     */
    function validateProductId(): array
    {
        $requiredParameters = ["product_id"];
        Log::logInfo("CartRequest","validateProductId","validated product id send from request ","success","no data");

        return $this->jsonInputValidate($requiredParameters);
    }

    /** 
     *    get product id from request
     *    @param
     *    @return string   
     */
    function getProductId(): string
    {
        $request = $this->getBody();
        $prodcutIdArray = json_decode($request['raw'], true);
        Log::logInfo("CartRequest","getProductId","get product id from request","success",$prodcutIdArray['product_id']);

        return $prodcutIdArray['product_id'];
    }

    /** 
     *    validate product id and quantity send from request 
     *    @param
     *    @return array   
     */
    function validateProductIdAndQuantity(): array
    {
        $requiredParameters = ["product_id", "quantity"];
        Log::logInfo("CartRequest","validateProductIdAndQuantity","validated product id and quantity send from request ","success","no data");

        return $this->jsonInputValidate($requiredParameters);
    }

    /** 
     *    get product id and quantity from request
     *    @param
     *    @return array   
     */
    function getProductIdAndQuantity(): array
    {
        $request = $this->getBody();
        $prodcutIdArray = json_decode($request['raw'], true);
        Log::logInfo("CartRequest","getProductIdAndQuantity","get product id and quantity from request","success","product id - {$prodcutIdArray['product_id']}; quantity - {$prodcutIdArray['quantity']}");

        return ['productId' => $prodcutIdArray['product_id'], 'quantity' => $prodcutIdArray['quantity']];
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