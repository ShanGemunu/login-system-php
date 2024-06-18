<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="/products" method="post">
        <button type="submit">submit</button>
    </form>
</body>
</html>




<?php 
// require __DIR__ . '/vendor/autoload.php';

// use Firebase\JWT\ExpiredException;
// use Firebase\JWT\SignatureInvalidException;
// use Firebase\JWT\BeforeValidException;
// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;

// function validate_jwt_token($jwt_token, $secret_key) {
//     try {
//         // return object having payload
//         return JWT::decode($jwt_token, new Key($secret_key, 'HS256'));  
//     } catch (ExpiredException $e) {
//         throw new Exception('Token expired');
//     } catch (SignatureInvalidException $e) {
//         throw new Exception('Invalid token signature');
//     } catch (BeforeValidException $e) {
//         throw new Exception('Token not valid yet');
//     } catch (Exception $e) {
//         throw new Exception('Invalid token');
//     }
// }

// if($_COOKIE["token-from-chrome"] === $_SESSION["token"]){
//     var_dump(validate_jwt_token($_SESSION["token"],"secret key shan"));
//     echo "<br>";
//     echo validate_jwt_token($_SESSION["token"],"secret key shan")->exp;
//     echo "<br>";
//     var_dump(validate_jwt_token($_SESSION["token"],"secret key shan")->exp);
// }else{
//     echo "invalid";
// }
