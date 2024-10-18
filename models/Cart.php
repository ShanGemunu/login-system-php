<?php

namespace app\models;

use app\core\Log;
use app\core\Application;

class Cart extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table("cart");
    }

    /** 
     *    get cart id for current user
     *    @return array
     */
    function getCartId(): string
    {
        $currentUserId = Application::$userId;
        $this->whereAnd("belonged_user", "=", $currentUserId);
        $cartIdArray = $this->select(['id'=>["id",null]]);
        Log::logInfo("Cart","getCartId","get cart id for current user","success","currentUserId : $currentUserId");

        return $cartIdArray[0]['id'];
    }
}