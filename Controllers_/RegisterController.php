<?php
require_once __DIR__.'/../Models/Models/Users.php';

class RegisterController
{
  function setRegisterPageStatus($status)
  {
    $_SESSION['registerPageStatus'] = $status;
  }

  // string, string, string -> [string, string, string]
  function sanitiseInputs($userName_, $email_, $password_){
    // sanitize inputs
    $userName = htmlspecialchars($userName_, ENT_QUOTES);
    // $userName = urlencode($userName);
    $email = htmlspecialchars($email_, ENT_QUOTES);
    // $email = urlencode($email);
    $password = htmlspecialchars($password_, ENT_QUOTES);
    // $password = urlencode($password);

    return ['userName'=>$userName, 'email'=>$email, 'password'=>$password];
  }

  // validate inputs entered ro register
  // string, string, string -> [bool, string]
  function validateInputs($userName, $email, $password)
  {
    $inputValidationResult = ['isInputsValidated'=>false,'inputValidationStatus'=>null];

    if (20 < strlen($userName) or 30 < strlen($email) or 20 < strlen($password)) {
      $inputValidationResult['inputValidationStatus'] = 'invalid inputs';
      return $inputValidationResult;
    }

    // validate user inputs   
    if (!($userName and $email and filter_var($email, FILTER_VALIDATE_EMAIL) and $password)){
      $inputValidationResult['inputValidationStatus'] = 'Make sure to fill all the required fields OR provide vaild email.';
      return $inputValidationResult;
    }

    $inputValidationResult['inputValidationStatus'] = true;
    $inputValidationResult['isInputsValidated'] = 'inputs validated';
    return $inputValidationResult;
  }

  // check user email provided is already in db
  // string -> bool
  function checkUserEmailIsExist($email){
    $users = new Users();
    $user = $users->checkUserEmailIsExist($email);
      if (count($user) > 0) {
        
        // if user email provided is already in db
        return true;
      }

      // if user email provided is not in db
      return false;
  }


  // insert new user email and password to db
  function insertNewUserNameTypeEmailAndPassword($userName, $email, $hashedPassword, $userType){
    $users = new Users();
    $users->insertNewUserNameTypeEmailAndPassword($userName, $email, $hashedPassword, $userType);
  }

}
       








