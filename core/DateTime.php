<?php

namespace app\core;

class DateTime
{
    function __construct($timeZone)
    {
        date_default_timezone_set($timeZone);
    }

    /** 
     *    return current date time
     *    @param string $format
     *    @return string   
     */
    public static function getCurrentDateTime(string $format) : string
    {
        $log_data = date($format);
        Log::logInfo("DateTime", "getCurrentDateTime", "return current date time", "success", "format - $format; return value - $log_data");

        return date($format);
    }

     /** 
     *    return past or future date time
     *    @param string $format
     *    @param int $day
     *    @param string $pastOrFuture
     *    @return string   
     */
    public static function getPastOrFutureDateTime(string $format, int $day, string $pastOrFuture = "+") : string
    {
        $log_data = date($format, strtotime('$pastOrFuture $day day'));
        Log::logInfo("DateTime", "getPastOrFutureDateTime", "return past or future date time", "success", "format - $format; day - $day; past or future - $pastOrFuture; return value - $log_data");

        return date($format, strtotime('$pastOrFuture $day day'));
    }

}