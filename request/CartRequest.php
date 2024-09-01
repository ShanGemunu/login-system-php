<?php

namespace app\request;

use app\core\Request;
use app\logs\Log;

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
        Log::logInfo("executing validateProductId at CartRequest");

        return $this->jsonInputValidation($requiredParameters);
    }

    /** 
     *    get product id from request
     *    @param
     *    @return string   
     */
    function getProductId() : string
    {
        $request = $this->getBody();
        $prodcutIdArray =  json_decode($request['raw'], true);
        Log::logInfo("executing getProductId at CartRequest");

        return $prodcutIdArray['product_id'];
    }

    /** 
     *    validate product id and quantity send from request 
     *    @param
     *    @return array   
     */
    function validateProductIdAndQuantity(): array
    {
        $requiredParameters = ["product_id","quantity"];
        Log::logInfo("executing validateProductIdAndQuantity at CartRequest");

        return $this->jsonInputValidation($requiredParameters);
    }

    /** 
     *    get product id and quantity from request
     *    @param
     *    @return array   
     */
    function getProductIdAndQuantity() : array
    {
        $request = $this->getBody();
        $prodcutIdArray =  json_decode($request['raw'], true);
        Log::logInfo("executing getProductIdAndQuantity at CartRequest");

        return ['productId'=>$prodcutIdArray['product_id'], 'quantity'=>$prodcutIdArray['quantity']];
    }
}