instruction to setup

1. download code files folder from repository and unzip it, then paste it into htdocs folder for xampp(if using any other server, paste into code file location of that server)
2. extract db file called loginsystem_php.sql from database folder.
3. execute that file in myqsql server.
4. change $servername, $username, $password and $port parameters in db-connection.php file accroding to your sql server configurations But leave  $dbName = "loginsystem_php" as it is.
5. to install and include bootstrap in project, navigate to project directory. then run "composer require twbs/bootstrap:5.3.3" in terminal to install bootstrap    
6. then run apache and sql servers in xampp or any other server you are using.
7. navigate to http://localhost/(copied folder name)/login.php route to navigate login page. you can naviagte other two pages(register,homapage) by replacing php file name to register.php or homepage.php in route.
8. you can only view homepage if user was already logged in. sample email and password -> email=mark@abc.com , password=mark1234 .
9. (optional) if styles is not added, make sure to change the paths in href attribute for link tag and src attributes in script tags as appropriate to where bootstrap is installed.   
