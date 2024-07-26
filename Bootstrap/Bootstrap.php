<?php
require_once __DIR__.'/../Logs/Logger.php';
require 'vendor/autoload.php';

class Bootstrap
{
    
    function startInitialServices()
    {
        function exception_handler(Throwable $e)
        {
            date_default_timezone_set('Asia/Colombo');
            $log = [date('Y-m-d , H:i:s') . " ", $e->getMessage() . " ", $e->getLine() . " ", $e->getFile()];
            $filePath = __DIR__ . '\logs\exception\global-exceptions\global-exceptions.csv';
            $fileHandle = fopen($filePath, 'a');
            fputcsv($fileHandle, $log);
            fclose($fileHandle);
            echo json_encode("error: system error, try again");
            exit;
        }

        // global exception handler 
        set_exception_handler('exception_handler');

        // start logger functionality
        $logger = new Logger();
        $logger->createLogId();
        
        // enable enviornment variables 
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
}

