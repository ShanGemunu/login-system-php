<?php
namespace app\controllers;

use app\core\Controller;
use app\core\Response;
use app\models\Products;
use app\request\ProductRequest;
use app\exceptions\FileContentInvalidException;
use app\exceptions\FileMovedFailedException;
use app\middlewares\AuthMiddleware;
use app\core\Log;
use Exception;

class ProductController extends Controller
{
    function __construct(){
        $this->registerMiddleware(new AuthMiddleware(['uploadProductsAsBulk']));
    }

    /** 
     *    get limited products from db by search value and limit  
     *    @param  
     *    @return string   
     */
    function getProductsByLimit()
    {
        try {
            $productRequest = new ProductRequest();
            $parameters = $productRequest->getParametersToGetProductsByLimit();

            $columns = ["id", "product_name", "price", "input_date", "quantity"];
            $orderColumn = $columns[$parameters['orderColumnIndex']];

            $productModel = new Products();
            $products = $productModel->getProductsByLimit($parameters['start'], $parameters['length'], $parameters['searchValue'], $orderColumn, $parameters['orderDir']);

            $filteredData = count($products);
            $totalRecords = 1000013;

            $response = [
                "column" => $parameters['orderColumnIndex'],
                "colName" => $orderColumn,
                "draw" => $parameters['draw'],
                "recordsTotal" => $filteredData,
                "recordsFiltered" => $totalRecords,
                "data" => $products
            ];
            Log::logInfo("ProductController","getProductsByLimit","get limited products","success","no data");

            return json_encode($response);
        } catch (Exception $exception) {
            Log::logError("ProductController","getProductsByLimit","Exception raised when trying to get limited products","failed",$exception->getMessage());
            $response = new Response();
            $response->setStatusCode(500);

            return "system error";
        }
    }

    /** 
     *    upload products as bulk to db
     *    @param  
     *    @throws FileMovedFailedException
     *    @throws FileContentInvalidException
     *    @return string   
     */
    function uploadProductsAsBulk(): string
    {
        try {
            $productRequest = new ProductRequest();
            $validateStatus = $productRequest->validateInsertProductsByInFile();

            if (!$validateStatus['isValidated']) {
                Log::logInfo("ProductController","uploadProductsAsBulk","validation failed","failed",$validateStatus['invalidReason']);

                return json_encode(['success' => false, 'result' => $validateStatus['invalidReason']]);
            }

            $file = $productRequest->getBulkProductFile();
            $targetDirectory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'files';
            $targetFile = $targetDirectory . DIRECTORY_SEPARATOR . 'products.csv';
            $isFileMoved = move_uploaded_file($file['tmp_name'], $targetFile);
            if (!$isFileMoved) {
                $currentDirectory = __DIR__;
                throw new FileMovedFailedException("when moving products.csv file from request into $currentDirectory\..\files\products.csv", "ProductController", "uploadProductsAsBulk");
            }

            // validating if file contains correct header format
            $file = fopen($targetFile, 'r');
            $header = fgets($file);
            fclose($file);
            $header = str_replace(" ", "", $header);
            $header = trim($header);

            if (!strpos($header, "product_name,price,link,quantity")) {
                throw new FileContentInvalidException("", "ProductController", "uploadProductsAsBulk");
            }

            $productModel = new Products();
            $productModel->insertProductsAsInFile();
            Log::logInfo("ProductController","uploadProductsAsBulk","products uploaded succesfully to db","success","no data");

            return json_encode(['success' => true, 'result' => "products uploaded successfully."]);
        } catch (FileContentInvalidException $exception) {
            Log::logError("ProductController","uploadProductsAsBulk","FileContentInvalidException Exception raised when trying to upload products as bulk file","failed",$exception->getMessage());

            return "File Content Invalid";
        } catch (Exception $exception) {
            Log::logError("ProductController","uploadProductsAsBulk","Exception raised when trying to upload products as bulk file","failed",$exception->getMessage());
            $response = new Response();
            $response->setStatusCode(500);

            return "system error";
        }
    }

    /** 
     *    render products page to front end 
     *    @return string   
     */
    function index()
    {
        try {
            $this->setLayout('main');
            Log::logInfo("ProductController","index","render products page to frontend","success","no data");

            return $this->render('products');
        } catch (Exception $exception) {
            Log::logError("ProductController","index","Exception raised when trying to render products view","failed",$exception->getMessage());
            $response = new Response();
            $response->setStatusCode(500);

            return "system error";
        }
    }
}