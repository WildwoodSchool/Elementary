<?php


require "datecheck.php";

mysql_connect("localhost", "zack", "denox111") or die(mysql_error());
$the_db = "elementary_".$year;
mysql_select_db($the_db) or die(mysql_error());
// echo $the_db.'<br>year: '.$_SESSION['year'].'<br>temp: '. $_SESSION['temp_year'].'<br>real: '.$_SESSION['real_year'];

// Log EVERYTHING
include('logger.php');

?>