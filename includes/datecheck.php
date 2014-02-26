<?php

// Get semester selection, guess if not selected
if((!isset($_SESSION['semester'])) && (!isset($_POST['semester'])) && (!isset($_GET['sem'])) ){
	if(((date(n)) >= 7) || ((date(n)) == 1)){
		$_SESSION['semester'] = 1;
	}
	else{
		$_SESSION['semester'] = 2;
	}
}
elseif(isset($_POST['semester'])){
	$_SESSION['semester'] = $_POST['semester'];
}
elseif(isset($_GET['sessionsem'])){
	$pageurl = $_SERVER['REQUEST_URI'];
	$_SESSION['semester'] = $_GET['sessionsem'];
	$pageurl = str_replace("&sessionsem=".$_SESSION['semester'],"",$pageurl);
	header ("Location: $pageurl");

}

//Figure out what year to save/print
if(!isset($_SESSION['year']) && (!isset($_POST['year']))){
	$month = date(n);
	if(($month >= 7) && ($_SESSION['semester']  == 1)){
		$year = date(Y) + 1;
	}
	if(($month < 7) && ($_SESSION['semester']  == 1)){
		$year = date(Y);
	}
	if($_SESSION['semester']  == 2){
		$year = date(Y);
	}
	$_SESSION['year'] = $year;
	$_SESSION['real_year'] = $year;
}
elseif(isset($_POST['year'])){
	$_SESSION['year'] = $_POST['year'];
}
$year = $_SESSION['year'];

?>