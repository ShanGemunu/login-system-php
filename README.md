instruction to setup

1. download code files folder from repository and unzip it, then paste it into htdocs folder for xampp(if using any other server, paste into code file location of that server)
2. extract db file called loginsystem_php.sql from database folder.
3. execute that file in myqsql server.
4. change $servername, $username, $password and $port parameters in db-connection.php file accroding to your sql server configurations But leave  $dbName = "loginsystem_php" as it is.
5. then run apache and sql servers in xampp or any other server you are using.
6. navigate to http://localhost/(copied folder name)/login.php route to navigate login page. you can naviagte other two pages(register,homapage) by replacing php file name to register.php or homepage.php in route.
7. you can only view homepage if user was already logged in. sample email and password -> email=mark@abc.com , password=mark1234 . 
