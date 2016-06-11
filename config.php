<?php
function getDB()
{
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "root";
$dbname = "scrumj";

$mysql_conn_string = "mysql:host=$dbhost;dbname=$dbname";
$dbConnection = new PDO($mysql_conn_string, $dbuser, $dbpass);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// $dbConnection->query("CREATE TABLE IF NOT EXISTS `comments` ( `id` int(11) NOT NULL,`picture_id` int(11) NOT NULL,`name` varchar(100) NOT NULL,  `story` text NOT NULL,  `picture` varchar(200) NOT NULL,  `active` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
// $dbConnection->query("CREATE TABLE IF NOT EXISTS `pictures` (  `id` int(11) NOT NULL,  `title` varchar(200) CHARACTER SET latin1 NOT NULL,  `place` varchar(200) CHARACTER SET latin1 NOT NULL,  `story` text CHARACTER SET latin1 NOT NULL,  `type` int(11) NOT NULL,  `picture` text CHARACTER SET latin1 NOT NULL,  `active` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
return $dbConnection;
}