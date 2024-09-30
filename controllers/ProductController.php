<?php
namespace app\controllers;

use app\core\Controller;
use app\core\Response;
use app\models\Products;
use app\request\ProductRequest;
use app\exceptions\FileContentInvalidException;
use app\exceptions\FileMovedFailedException;
use app\exceptions\FileCopyFailedException;
use app\middlewares\AuthMiddleware;
use app\middlewares\UsertypeMiddleware;
use app\core\Log;
use app\traits\File;
use app\core\DateTime;
use app\core\Application;
use Exception;

class ProductController extends Controller
{
    use File;
    function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(["uploadProductsAsBulk"]));
        $this->registerMiddleware(new UsertypeMiddleware(
            [
                'user' => ["uploadProductsAsBulk"]
            ]
        ));
    }

    /** 
     *    get limited products from db by search value and limit  
     *    @param  
     *    @return string   
     */
    public function getProductsByLimit()
    {
        try {
            $productRequest = new ProductRequest();
            $parameters = $productRequest->getParametersToGetProductsByLimit();

            $columns = ["id", "product_name", "price", "input_date", "quantity"];
            $orderColumn = $columns[$parameters['orderColumnIndex']];

            $productModel = new Products();
            $products = $productModel->getProductsByLimit($parameters['start'], $parameters['length'], $parameters['searchValue'], $orderColumn, $parameters['orderDir']);

            $filteredData = count(value: $products);
            $totalRecords = 1000013;

            foreach ($products as &$product) {
                ob_start();
                include Application::$ROOT_DIR . "/views/components/product-edit-form.php";
                $form = ob_get_clean();
                $form = str_replace('{{formId}}', $product['id'], $form);
                $product['editPopup'] = Application::$app->view->buildCustomComponent("popup", ['triggerButton' => "edit", 'title' => "Edit prodcut", 'body' => $form]);
            }

            Log::logInfo("ProductController", "getProductsByLimit", "build edit button and popup for each of products to be returned", "success", "no data");

            $response = [
                "draw" => $parameters['draw'],
                "recordsTotal" => $filteredData,
                "recordsFiltered" => $totalRecords,
                "data" => $products
            ];
            Log::logInfo("ProductController", "getProductsByLimit", "get limited products", "success", "no data");
            Application::$app->response->setStatusCode(200);

            return json_encode($response);
        } catch (Exception $exception) {
            Log::logError("ProductController", "getProductsByLimit", "Exception raised when trying to get limited products", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }
    }

    /** 
     *    upload products as bulk to db
     *    @throws FileMovedFailedException
     *    @throws FileContentInvalidException
     *    @throws FileCopyFailedException
     *    @return string   
     */
    function uploadProductsAsBulk(): string
    {
        try {
            $productRequest = new ProductRequest();
            $validateStatus = $productRequest->validateInsertProductsByInFile();

            if (!$validateStatus['isValidated']) {
                Log::logInfo("ProductController", "uploadProductsAsBulk", "validation failed", "failed", $validateStatus['invalidReason']);
                Application::$app->response->setStatusCode(422);

                return json_encode(['success' => false, 'result' => $validateStatus['invalidReason']]);
            }

            $file = $productRequest->getBulkProductFile();
            $header = $this->getFirstLine($file['tmp_name']);
            $header = str_replace(" ", "", $header);
            $header = trim($header);

            if (!strpos($header, "product_name,price,link,quantity")) {
                throw new FileContentInvalidException("File header invalid", "ProductController", "uploadProductsAsBulk");
            }
            $targetFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'temp-files' .
                DIRECTORY_SEPARATOR . 'temp_file.csv';
            $isFileMoved = $this->fileMove($file['tmp_name'], $targetFile);

            if (!$isFileMoved) {
                throw new FileMovedFailedException("when moving products file from -request- into -temp file directory for db load infile-", "ProductController", "uploadProductsAsBulk");
            }

            $productModel = new Products();
            $productModel->insertProductsAsInFile();
            Log::logInfo("ProductController", "uploadProductsAsBulk", "products uploaded succesfully to db", "success", "no data");

            $productFileName = DateTime::getCurrentDateTime("Ymd_His") . ".csv";
            $destination = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $productFileName;
            $isFileCopy = $this->fileCopy($targetFile, $destination);
            
            if (!$isFileCopy) {
                throw new FileCopyFailedException("when copy products file from -temp file directory for db load infile-  to -file name of $productFileName in products file directory-", "ProductController", "uploadProductsAsBulk");
            }
            Log::logInfo("ProductController", "uploadProductsAsBulk", "create new bulk product file and store data successfully", "success", "no data");
            Application::$app->response->setStatusCode(200);

            return json_encode(['success' => true, 'result' => "products uploaded successfully."]);
        } catch (FileContentInvalidException $exception) {
            Log::logError("ProductController", "uploadProductsAsBulk", "FileContentInvalidException Exception raised when trying to upload products as bulk file", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(422);

            return json_encode(['success' => false, 'result' => "File header invalid."]);
        } catch (Exception $exception) {
            Log::logError("ProductController", "uploadProductsAsBulk", "Exception raised when trying to upload products as bulk file", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

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
            $this->setLayout(layout: 'main');
            Log::logInfo("ProductController", "index", "render products page to frontend", "success", "no data");
            Application::$app->response->setStatusCode(code: 200);

            return $this->render('products');
        } catch (Exception $exception) {
            Log::logError("ProductController", "index", "Exception raised when trying to render products view", "failed", $exception->getMessage());
            Application::$app->response->setStatusCode(500);

            return "system error";
        }
    }
}