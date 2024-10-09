<?php

namespace app\models;

use app\core\Log;

class Cart extends BaseModel
{
    function __construct()
    {
        parent::__construct();
        $this->table("cart");
    }

    /** 
     *    get cart id for current user
     *    @param int $currentUserId
     *    @return array
     */
    function getCartId(int $currentUserId): string
    {
        $this->whereAnd("belonged_user", "=", $currentUserId);
        $cartIdArray = $this->select(['id'=>["id",null]]);
        Log::logInfo("Cart","getCartId","get cart id for current user","success","currentUserId : $currentUserId");

        return $cartIdArray[0]['id'];
    }
}