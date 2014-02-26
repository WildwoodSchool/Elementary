<?php

// Connect to elementary DB
require "database.php";

// Get Array Values
include "arrays.php";

// Start Session
session_start();
   
// Logout
if($_GET['act'] == 'logout') {
	session_destroy();
	header("Location: /elementary/login.php");
}

// Failed attempt at using Zend auth. QQ
// 	$auth = Zend_Auth::getInstance();
// 	$username = str_replace("WILDWOOD\\", "",$auth->getIdentity());
//     echo $username;

// So get SESSION info instead
// $username = $_SESSION['username'];
// $userid = $_SESSION['userid'];

// Test account
$username = "zshaffer";
$userid = "EMP000254";
	
	
}		

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Wildwood School :: Online Evaluation System</title>
    <link href="/css/main.css" media="screen" rel="stylesheet" type="text/css" />    <script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui-1.8.21.custom.min.js"></script>
    <script type="text/javascript" src="/js/ajax.js"></script>
    <script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
    <link rel="stylesheet" type="text/css" media="all"
          href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"    />
<body>

<div id="wrapper">
    <div id="header">

        <div id="userPanel">


        </div>
        <div id="searchFloat">

        <input type="text" size="20" onkeyup="showResult(this.value)" id="searchInput" style="width:120px;" />


                <div id="livesearch"></div></div>
                <img src="/images/assessmentsLogo.png" style="padding:10px 20px 30px 20px;">
<?php

// Echo Navbar
include "navbar.php";
?>
    <div id="content">
    <div id="aside" style="margin-right:20px; height: 48px;"><form name="classform" id="classform" method="POST"><ul><li style="background: rgb(170,170,170);}" >Select a Class:


<?php


?>