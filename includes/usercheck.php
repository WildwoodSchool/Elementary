<?php

// Start Session
session_start();

if(isset($_POST['masq'])) {
	if(!isset($_SESSION['masq'])) {
		$_SESSION['masq'] = $_SESSION['user'];
	}
	$_SESSION['user'] = $_SESSION['elem_teacher_array'][($_POST['masq'])];
	header ("Location: /elementary/index.php");
}


// Check if has logged in
if(!isset($_SESSION['user'])) {
	header ("Location: /elementary/login.php");
}

// If user has logged in, get username and id
else{

	// Check if user came from main login, in which case get Elementary flags
	if(!isset($_SESSION['user']['flags'])) {
		
		mysql_connect("localhost", "zack", "denox111") or die(mysql_error());
		mysql_select_db("elementary") or die(mysql_error());	
	
		$sql = "SELECT * FROM teacher_accounts WHERE username = '".$_SESSION['user']['name']."'";
		$result = mysql_query($sql) or die (mysql_error());
		$row = mysql_fetch_assoc($result);
		$_SESSION['user']['flags'] = $row['flags'];
		if($row['flags'] == "r"){    
			header ("Location: /elementary/admin/reader.php");
		}
		if($row['flags'] == "d"){
			$_SESSION['user'] = null;
			header ("Location: /elementary/login.php");
		}
	}


	$user = $_SESSION['user'];
}

// Logout Action
if($_GET['act'] == 'logout') {
	session_destroy();
	header("Location: /elementary/login.php");
}

// Logout Action
if($_GET['act'] == 'unmasq') {
	$_SESSION['user'] = $_SESSION['masq'];
	$_SESSION['masq'] = null;
	header("Location: /elementary/index.php");
}


?>