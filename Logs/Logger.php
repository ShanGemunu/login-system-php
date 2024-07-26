<?php
class Logger
{
    // log contains logid, date and time, logtype(sucess, exception), content, class, function, line, file

    // used to set log id for every request,   
    // Note: if log id created at same time for ununthenticate users 
    // same log id can be generated.
    function createLogId()
    {
        $currentUserId = "0000";
        if (isset($_SESSION['currentUser'])) {
            $currentUserId = $_SESSION['currentUser'];
        }

        $id = date('Ymd') ."-". $currentUserId."-";

        // set log id 
        $_SESSION['logId'] = uniqid($id);
    }

    function createLog($logType, $content, $class, $function, $line, $file)
    {
        date_default_timezone_set('Asia/Colombo');
        if ($logType === "success") {
            $filePath = __DIR__ . '/../logs/success/' . 'success_' . date('Y_m_d').'.csv';
            $fileHandle = fopen($filePath, 'a');

            if ($fileHandle) {
                // logid, date and time, logtype(sucess, exception), content, class, function, line, file
                $log = [$_SESSION['logId'], date('Y-m-d , H:i:s'), $logType, $content, $class, $function, $line, $file];

                // Write the data to the CSV file
                fputcsv($fileHandle, $log);

                // Close the file
                fclose($fileHandle);
            } else {
                echo "Failed to open file!";
            }
        } else {
            $filePath = __DIR__ . '/../logs/exception/' . 'exception_' . date('Y_m_d').'.csv';
            $fileHandle = fopen($filePath, 'a');

            if ($fileHandle) {
                // logid, date and time, logtype(sucess, exception), content, class, function, line, file
                $log = [$_SESSION['logId'], date('Y-m-d , H:i:s'), $logType, $content, $class, $function, $line, $file];

                // Write the data to the CSV file
                fputcsv($fileHandle, $log);

                // Close the file
                fclose($fileHandle);
            } else {
                echo "Failed to open file!";
            }
        }
        ;
    }
}