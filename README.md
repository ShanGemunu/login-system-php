instruction to setup

1. extract db file called loginsystem_php.sql from database folder.
2. execute that file in myqsql server.
3. change $servername, $username, $password and $port parameters accroding to your sql server configurations But leave  $dbName = "loginsystem_php" as it is.
4. then run apache and sql servers in xampp or any other server you are using.
5. navigate to http://localhost/login-system-php/login.php route to navigate login page. you can naviagte other two pages(register,homapage) by replacing php file name to register.php or homepage.php in route.
6. you can only view homepage if user was already logged in. 
