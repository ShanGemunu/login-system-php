<?php
require_once __DIR__ . '/../Middleware/Authentication.php';
require_once __DIR__ . '/../Controllers/LoginController.php';
require_once __DIR__ . '/../Controllers/RegisterController.php';

class RoutesHandler
{
    function navigateRoutes_Unregistered_User($request, $method)
    {
        switch ([$request, $method]) {

            case ['/login', 'GET']:
                require_once (__DIR__ . '\Views\LoginView.php');
                $loginController = new LoginController();
                $loginController->setLoginPageStatus(null);
                break;

            case ['/login', 'POST']:
                $email = $_POST['email'];
                $password = $_POST['password'];

                $authentication = new Authentication();
                $authenticateStatus = $authentication->authenticateUser($email, $password);

                if (!$authenticateStatus['isValidCredentials']) {
                    $loginController = new LoginController();
                    $loginController->setLoginPageStatus($authenticateStatus['outputStatus']);
                    header('Location:/login');
                    break;
                }

                $authentication->setAuthVariables($authenticateStatus['userDetails']);
                header('Location:/homepage');
                break;

            case ['/register', 'GET']:
                require_once (__DIR__ . 'Views\RegiterPage.php');
                $registerController = new RegisterController();
                $registerController->setRegisterPageStatus(null);
                break;

            case ['/register', 'POST']:
                $userName = $_POST['user_name'];
                $email = $_POST['email'];
                $password = $_POST['password'];

                $registerController = new RegisterController();

                // sanitise inputs
                $sanitizedInputs = $registerController->sanitiseInputs($userName, $email, $password);

                // check if inputs validated
                $inputValidationResult = $registerController->validateInputs($sanitizedInputs['userName'], $sanitizedInputs['email'], $sanitizedInputs['password']);

                if (!$inputValidationResult['isInputsValidated']) {
                    if ($inputValidationResult['inputValidationStatus'] === 'invalid inputs') {
                        $registerController->setRegisterPageStatus("invalid inputs");
                        header('Location:/register');
                    } else {
                        $registerController->setRegisterPageStatus("Make sure to fill all the required fields OR provide vaild email.");
                        header('Location:/register');
                    }
                    break;
                }

                // check if provided user email is already in db
                $isUserEmailExist = $registerController->checkUserEmailIsExist($sanitizedInputs['email']);
                if ($isUserEmailExist) {
                    $registerController->setRegisterPageStatus("Email provided already registered, try different email.");
                    header('Location:/register');
                    break;
                }

                // if provided email is not in db then register email and password
                $hashedPassword = password_hash($sanitizedInputs['password'], PASSWORD_DEFAULT);
                $registerController->insertNewUserNameTypeEmailAndPassword($sanitizedInputs['userName'], $sanitizedInputs['email'], $hashedPassword, 'seller');
                header('Location:/homepage');

                break;

            default:
                require_once (__DIR__ . '\NotFoundPage.php');
                break;
        }
    }

    function navigateRoutes_Registered_User($request, $method, $userType)
    {
        // get requests to /users to view all users access only for admins
        // hardcoded users
        // case ['/users', 'GET']:
        //     if ($isUserAuthenticated and $userType === "admin") {
        //         require_once (__DIR__ . '\Views\UsersPage.php');
        //     } else {
        //         header("Location:/error");
        //     }

        //     break;
    }
}
