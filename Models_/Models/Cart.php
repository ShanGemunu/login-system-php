<?php 

class Cart extends BaseModel{
    function __construct(){
        parent::__construct();
    }
    function getCartProducts($currentUser)
    {
        $returnArray = array("status" => null, "result" => null);
        $query = "SELECT * FROM cart_details WHERE belonged_user=? ";
        $statement = $this->conn->prepare($query);
        if ($statement === false) {
            $returnArray["status"] = false;
            $returnArray["result"] = "coundn't prepair statement!";
            return $returnArray;
        }

        $statement->bind_param("i", $currentUser);

        if ($statement->execute() === false) {
            $returnArray["status"] = false;
            $returnArray["result"] = "query failed.";
            return $returnArray;
        }

        $result = $statement->get_result();
        $result = $result->fetch_all(MYSQLI_ASSOC);
        $returnArray["status"] = true;
        $returnArray["result"] = $result;
        return $returnArray;
    }
}