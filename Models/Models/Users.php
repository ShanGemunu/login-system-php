<?php
require_once __DIR__.'/BaseModel.php';

class Users extends BaseModel{
    function __construct(){
        parent::__construct();
    }

    // string, string, string -> 
    function insertNewUserNameTypeEmailAndPassword($userName, $email, $hashedPassword, $userType)
    {
            $query = "INSERT INTO users (user_name, email, hashed_password, user_type)
            VALUES (?,?,?,?)";
            $statement = $this->conn->prepare($query);

            if ($statement === false) {
                throw new SqlFailedPrepair();
            }

            $statement->bind_param("sss", $userName, $email, $hashedPassword);

            if ($statement->execute() === false) {
                throw new SqlQueryFailed();
            }

            $result = $statement->get_result();
            $result = $result->fetch_all(MYSQLI_ASSOC);

    }

    // get user for provided email if email is in db
    function checkUserEmailIsExist($email)
    {
            $query = "SELECT * FROM users WHERE email=?";
            $statement = $this->conn->prepare($query);

            if ($statement === false) {
                throw new SqlFailedPrepair();
            }

            $statement->bind_param("s", $email);

            if ($statement->execute() === false) {
                throw new SqlQueryFailed();
            }

            $result = $statement->get_result();
            $result = $result->fetch_all(MYSQLI_ASSOC);

            return $result;
    }

    function getUserId($email)
    {
        try {
            $query = "SELECT id FROM users WHERE email=?";
            $statement = $this->conn->prepare($query);

            if ($statement === false) {
                throw new SqlFailedPrepair();
            }

            $statement->bind_param("s", $email);

            if ($statement->execute() === false) {
                throw new SqlQueryFailed();
            }

            $result = $statement->get_result();
            $result = $result->fetch_all(MYSQLI_ASSOC);

            return $result;
        } catch (Exception $e) {
            header("Location: /system/error");
            // add log 
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "getUserId", $e->getLine(), $e->getFile());
            exit;
        }

    }

    function getUserIdforOrder($orderId)
    {
        try {
            $query = "SELECT user_id FROM orders WHERE id=?";
            $statement = $this->conn->prepare($query);
            if ($statement === false) {
                throw new SqlFailedPrepair();
            }

            $statement->bind_param("s", $orderId);

            if ($statement->execute() === false) {
                throw new SqlQueryFailed();
            }

            $result = $statement->get_result();
            $result = $result->fetch_all(MYSQLI_ASSOC);
            return $result;
        } catch (SqlFailedPrepair $e) {
            header('Content-Type: application/json; charset=utf-8');
            $output = "error";
            echo json_encode($output);

            // add log 
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "getUserIdforOrder", $e->getLine(), $e->getFile());
            exit;
        } catch (SqlQueryFailed $e) {
            header('Content-Type: application/json; charset=utf-8');
            $output = "error";
            echo json_encode($output);

            // add log 
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "getUserIdforOrder", $e->getLine(), $e->getFile());
            exit;
        }

    }
}