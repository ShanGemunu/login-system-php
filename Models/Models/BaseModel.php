<?php
require_once __DIR__.'/../Connecter.php';

class BaseModel{
    private $conn;
    function __construct(){
        $Connecter = Connecter::getConneterInstance();
        $conn = $Connecter->getDbConnection();
    }
}