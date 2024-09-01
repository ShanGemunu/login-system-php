<?php
namespace app\controllers;

use app\core\Controller;
use app\core\Response;
use app\models\Products;
use app\request\ProductRequest;
use app\exceptions\FileContentInvalidException;
use app\exceptions\FileMovedFailedException;
use app\logs\Log;
use Exception;

class ProductController extends Controller
{
    /** 
     *    get limited products from db by search value and limit  
     *    @param  
     *    @return string   
     */
    function getProductsByLimit()
    {
        try {
            $productRequest = new ProductRequest();
            $validateStatus = $productRequest->validateRequestParametersToGetProductsByLimit();

            if(!$validateStatus['isValidated']){
                Log::logInfo("validation failed beacause of {$validateStatus['invalidReason']} at getProductsByLimit method of ProductController");

                return $validateStatus['invalidReason'];
            }

            $parameters = $productRequest->getRequestParametersToLoadProductsByLimit();
            $productModel = new Products();
            $products = $productModel->getProductsByLimit($parameters['offset'],$parameters['limit'],$parameters['keyword'],$parameters['column'],$parameters['order']);
            Log::logInfo("get limited products at getProductsByLimit method of ProductController");

            return json_encode($products);
        } catch (Exception $e) {
            Log::logError("Exception raised when trying to get limited products at getProductsByLimit method of ProductController as " . $e->getMessage());

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
    function uploadProductsAsBulk() : string 
    {
        try {
            $productRequest = new ProductRequest();
            $validateStatus = $productRequest->validateInsertProductsByInFile();

            if (!$validateStatus['isValidated']) {
                Log::logInfo("validation failed beacause of {$validateStatus['invalidReason']} at uploadProductsAsBulk method of ProductController");

                return $validateStatus['invalidReason'];
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
            $file = fopen($targetFile , 'r');
            $header = fgets($file);
            fclose($file);
            $header = str_replace(" ", "", $header);
            $header = trim($header);

            if (!strpos($header, "product_name,price,link,quantity")) {
                throw new FileContentInvalidException("", "ProductController", "uploadProductsAsBulk");
            }

            $productModel = new Products();
            $productModel->insertProductsAsInFile();
            Log::logInfo("products uploaded succesfully to db at uploadProductsAsBulk method of ProductController");

            /*
            //
            //
            need to send view
            //
            //
            */

            return "products uploaded successfully.";
        } 
        catch (FileContentInvalidException $e){
            Log::logError("FileContentInvalidException Exception raised when trying to upload products as bulk file at uploadProductsAsBulk method of ProductController as " . $e->getMessage());

            return "File Content Invalid";
        }catch (Exception $e) {
            Log::logError("Exception raised when trying to upload products as bulk file at uploadProductsAsBulk method of ProductController as " . $e->getMessage());

            return "system error";
        }
    }
}