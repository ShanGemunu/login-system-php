<?php 

class Products extends BaseModel{
    function __construct(){
        parent::__construct();
    }
    function fetchAllProducts()
    {
        $query = "SELECT * FROM products";
        try {
            return $this->conn->execute_query($query)->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            header("Location: /system/error");
            // add log 
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "fetchProducts", $e->getLine(), $e->getFile());
            exit;
        }

    }

    function fetchSelectedProductsByDataTables($start, $length, $searchValue, $orderColumn, $orderDir)
    {
        try {
            // SELECT * FROM users WHERE true AND (user_name LIKE '%%' OR email LIKE '%%') ORDER BY reg_date desc LIMIT 1,3;
            $query = "SELECT * FROM products WHERE true 
            AND (id LIKE ? OR product_name LIKE ? OR price LIKE ? OR input_date LIKE ? OR quantity LIKE ?) 
            ORDER BY price ASC 
            LIMIT ?, ?";

            $statement = $this->conn->prepare($query);

            if ($statement === false) {
                throw new SqlFailedPrepair();
            }

            $searchTerm = "%" . $searchValue . "%";
            $orderColumn_ = "price";
            $statement->bind_param("sssssii", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $start, $length);
           
            if ($statement->execute() === false) {
                throw new SqlQueryFailed();
            }

            $result = $statement->get_result();
            $result = $result->fetch_all(MYSQLI_ASSOC);

            return $result;
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            $output = "error: system error";
            echo json_encode($output);

            // add log 
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "fetchProductsPagination", $e->getLine(), $e->getFile());
            exit;
        }
    }

    function insertProducts()
    {
        try {
            $query = "
            LOAD DATA INFILE '../../htdocs/login-system-php/cache/products.csv' 
            IGNORE INTO TABLE new_db_login_system.products 
            CHARACTER SET UTF8 
            FIELDS TERMINATED BY ',' 
            LINES TERMINATED BY '\r\n' 
            IGNORE 1 LINES 
            (product_name, price, link, quantity)
            ";

            if (!($this->conn->query($query) === TRUE)) {
                throw new Exception();
            }

            return true;
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            $output = "operation failed, try again";
            echo json_encode($output);

            // add log 
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "insertProducts", $e->getLine(), $e->getFile());
            exit;
        }
    }

    function getMinProductId()
    {
        $query = "SELECT MIN(id) FROM products";
        return $this->conn->execute_query($query)->fetch_all(MYSQLI_ASSOC);
    }

    function getMaxProductId()
    {
        $query = "SELECT MAX(id) FROM products";
        return $this->conn->execute_query($query)->fetch_all(MYSQLI_ASSOC);
    }

    function updateProducts($productsIdsAndNewQuantities)
    {
        $output = ['isProductsUpdated' => false, 'message' => null];
        try {
            $query = "UPDATE products SET quantity=? WHERE id=?";
            $statement = $this->conn->prepare($query);
            if ($statement === false) {
                throw new SqlFailedPrepair("failed prepair");
            }

            foreach ($productsIdsAndNewQuantities as $item) {
                $statement->bind_param("ii", $item[1], $item[0]);
                if ($statement->execute() === false) {
                    throw new SqlQueryFailed("query execute failed");
                }
            }

            $output['isProductsUpdated'] = true;
            $output['message'] = "Products updated successfully";
            return $output;
        } catch (SqlFailedPrepair $e) {
            $output['isProductsUpdated'] = false;
            $output['message'] = $e->getMessage();

            // add log 
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "updateProducts", $e->getLine(), $e->getFile());

            return $output;
        } catch (SqlQueryFailed) {
            $output['isProductsUpdated'] = false;
            $output['message'] = $e->getMessage();

            // add log 
            $logger = new Logger();
            $logger->createLog("exception", $e->getMessage(), "Queries", "updateProducts", $e->getLine(), $e->getFile());

            return $output;
        }

    }
}