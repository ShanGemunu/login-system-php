<?php

class LoginController
{
  function setLoginPageStatus($status)
  {
    $_SESSION['loginPageStatus'] = $status;
  }
}







