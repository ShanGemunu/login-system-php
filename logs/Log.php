<?php

namespace app\logs;

date_default_timezone_set('Asia/Colombo');

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class Log
{
    private static $logger = null;

    public static function getLogger()
    {
        if (self::$logger === null) {
            self::$logger = new Logger('app');
            
            // Define a custom date format and output format
            $dateFormat = 'Y-m-d H:i:s';
            $outputFormat = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            
            // Create a formatter with the custom formats
            $formatter = new LineFormatter($outputFormat, $dateFormat);

            // Create a handler with the custom formatter
            $streamHandler = new StreamHandler(__DIR__ . '/logs/app.log');
            $streamHandler->setFormatter($formatter);

            self::$logger->pushHandler($streamHandler);
        }

        return self::$logger;
    }

    public static function logInfo($message)
    {
        self::getLogger()->info($message);
    }

    public static function logError($message)
    {
        self::getLogger()->error($message);
    }
}











    
