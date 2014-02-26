<?php

exec("CREATE DATABASE elementary_2015;");
// mysql_query($newdbsql1) or die (mysql_error("nope"));


exec("mysqldump -h localhost -u zack -pdenox111 elementary_2013 | mysql -h localhost -u zack -pdenox111 elementary_2015");
// mysql_query($newdbsql2) or die (mysql_error("nope"));

?>
