<!-- https://github.com/lincanbin/PHP-PDO-MySQL-Class#install -->
<?php
define('DBHost', 'fdb34.awardspace.net');
define('DBPort', 3306);
define('DBName', '4154120_project');
define('DBUser', '4154120_project');
define('DBPassword', 'dj42570i');
require(__DIR__ . "/src/PDO.class.php");
$DB = new Db(DBHost, DBPort, DBName, DBUser, DBPassword);
?>