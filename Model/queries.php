<?php

// fetch_assoc , fetch_all
class Queries{
    public $conn;

    function __construct($conn){
        $this->conn = $conn;
    }
    
    function testDb(){

        $query = "SELECT orders.id, orders.order_date, products.product_name, products.price, order_details.quantity,
        orders.payment_method, orders.state FROM orders INNER JOIN order_details ON orders.id=order_details.order_id
        INNER JOIN products ON products.id = order_details.product_id WHERE orders.user_id=0
        ORDER BY order_details.quantity";

        $statement = $this->conn->prepare($query);

        // $statement = "select product_name from products where id=?";

        // $statement = $this->conn->prepare($statement);
        // $idOne = '200000';
        // $idTwo = 104;
        // $statement-> bind_param("i", $idTwo);
        $statement->execute();
        $result = $statement->get_result();

        $data = $result->fetch_all();

        return $data;


        // $data = $result->fetch_all(MYSQLI_ASSOC);  // return array [[with parametered names]],,,,,  
        // if used $result->fetch_all() -> array [[with indexes]]


        // var_dump($data[0][0]);
    



        // $sql = "SELECT * FROM products"; // SQL with parameters 
        
        // return $this->conn->execute_query($sql)->fetch_all(); // type array

        // var_dump(json_encode($result));

        // echo file_put_contents(__DIR__ . '\data\chache.json',json_encode($result));
       
        // var_dump(json_decode(file_get_contents(__DIR__ . '\..\Controller\chache6.json'), true));
       
        // foreach($result as $row){
        //     var_dump($row);  // type array
        //     echo "<br>";
        // }
    }

    function getOrdersForUser(){ // date payment method state products quantity  
        $query = "SELECT orders.id, orders.order_date, products.product_name, products.price, order_details.quantity,
        orders.payment_method, orders.state FROM orders INNER JOIN order_details ON orders.id=order_details.order_id
        INNER JOIN products ON products.id = order_details.product_id WHERE orders.user_id = ? ORDER BY orders.id";

        $statement = $this->conn->prepare($query);  

        if($statement === false){
            return [false,"coundn't prepair statement!"];
        }    

        $currentUserId = $this->getUserId();

        if($currentUserId[0] === false) return [false,"failed"];

        $statement->bind_param("i",$currentUserId[1][0][0]);

        if($statement->execute()===false){
            return [false,"query failed."];
        }

        $result = $statement->get_result();
        $result = $result->fetch_all();  //  get empty array [] if user id is not in orders table,  if user id is in orders table then all order details shows for all orders of user[[order_details-1],[order_details-2],..]
    
        return [true,$result];

    }

    function getMinProductId(){
        $query = "SELECT MIN(id) FROM products";
        return $this->conn->execute_query($query)->fetch_all();
    }

    function getMaxProductId(){
        $query = "SELECT MAX(id) FROM products";
        return $this->conn->execute_query($query)->fetch_all();
    }
    
    function checkUserIsExist($email){
        $query = "SELECT * FROM users WHERE email='".$email."'";
        return $this->conn -> query($query);
    }
    
    function insertNewUser($email, $hashedPassword){
        $query = "INSERT INTO users (user_name, email, hashed_password)
        VALUES ('".$_POST['user_name']."','".$_POST['email']."','".$hashedPassword."')";
    
        return $this->conn -> query($query);
    }

    function fetchProducts(){
        $query = "SELECT * FROM products";
        return $this->conn->execute_query($query)->fetch_all();
    }

    // array -> array
    function updateProducts($productsIdsAndNewQuantities){
        $query = "UPDATE products SET quantity=? WHERE id=?";
        $statement = $this->conn->prepare($query);
        if($statement === false){
            return [false,"coundn't prepair statement!"];
        }    
    
        foreach($productsIdsAndNewQuantities as $item){
            $statement->bind_param("ii", $item[1], $item[0]);
            if($statement->execute()===false){
                return [false,$item];
            }
        }
        
        return [true, "products sucessfully updated."];
    }

    function getUserId(){
        $query = "SELECT id FROM users WHERE email=?";
        $statement = $this->conn->prepare($query);
        if($statement === false){
            return [false,"coundn't prepair statement!"];
        } 

        $statement->bind_param("s",$_SESSION['currentUser']);

        if($statement->execute()===false){
            return [false,"query failed."];
        }

        $result = $statement->get_result();
        $result = $result->fetch_all();

        return [true,$result];  
    }

    function addOrderLog(){
        $query = "INSERT INTO orders (payment_method, user_id, state) VALUES (?,?,?)";
        $statement = $this->conn->prepare($query);
        if($statement === false){
            return [false,"coundn't prepair statement!"];
        } 
        // get current user id
        $currentUserId = $this->getUserId()[0][0];
        $paymentMethod = "debit-card";
        $status = "pending";

        $statement->bind_param("sis",$paymentMethod, $currentUserId , $status);

        // add log to db
        if($statement->execute()===false){
            return [false,"query failed."];
        }
        
        return true; 
    }
    
    // add only one order detail record per one query
    function addOrderDetailLog($queryValuesString){
        $query = "INSERT INTO order_details (order_id, product_id, quantity) VALUES ". $queryValuesString;
        $statement = $this->conn->prepare($query);
        if($statement === false){
            return [false,"coundn't prepair statement!"];
        } 
        
        // try to add order detail log to db
        if($statement->execute()===false){
            return [false,"query failed."];
        }
        
        return [true];

    }

    function getRecentOrderId(){
        $query = "SELECT MAX(id) FROM orders";
        $statement = $this->conn->prepare($query);
        if($statement === false){
            return [false,"coundn't prepair statement!"];
        } 
    
        // add log to db
        if($statement->execute()===false){
            return [false,"query failed."];
        }

        $result = $statement->get_result();
        return $result->fetch_all()[0][0];  // return max order id
    }
}

