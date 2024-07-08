<?php
// require __DIR__ . '\vendor\autoload.php';
require_once('Model/DbConnection.php');
require_once('Model/queries.php');

require_once __DIR__ . '/vendor/autoload.php';

// instantiate and use the dompdf class
$dompdf = new Dompdf\Dompdf();
$dompdf->loadHtml('hiiiiiiiiii from php world');

// (Optional) Setup the paper size and orientation
// $dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

$name = "hi.pdf";

// Output the generated PDF to Browser
$pdf = $dompdf->output();
$paper = __DIR__ . '\public\assets\BI.pdf';

if(file_exists($paper)){
    header('Content-Description: File Transfer');
    header("Access-Control-Allow-Origin: http://localhost:5500
    ");
    header("Access-Control-Allow-Methods: GET");
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.$name.'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($paper));
    readfile($paper);
}else{
    echo var_dump($paper);
}










// var_dump($_SESSION['apiData']);
// var_dump($_SESSION['apiData']["id"]);

// $dbConn = new DbConnection();

// $queries = new Queries($dbConn->conn);

// $results = $queries->testDb();

// foreach($results as $result){
//     var_dump($result);
// }


// use Firebase\JWT\JWT;
// echo "<h3>hello php</h3>";
// setcookie("test-cookie", "", time()-3600);
// header("HTTP/1.1 302 founding...");

// var_dump($results);


// $result = file_get_contents("cache-test.txt");   

// var_dump(json_decode($result,true));

// foreach($result as $re){
//     var_dump($re);
//     echo "<br>";
// }

// echo "<br>";

// $results = json_encode($results);

// file_put_contents("cache-test.txt",json_encode(456));


// function generate_jwt_token($user_id,$userType, $secret_key) {
//     $issued_at = time();
//     $expiration_time = $issued_at + (60*60); // valid for 1 hour

//     $payload = array(
//         'iat' => $issued_at,
//         'exp' => $expiration_time,
//         'userId' => $user_id,
//         'userType' => $userType
//     );

//     return JWT::encode($payload, $secret_key, 'HS256');
// }

// $tokenOne = generate_jwt_token("shan","admin","secret key shan");

// $_SESSION["token"] = $tokenOne;
// setcookie("token-from-chrome",$tokenOne);
