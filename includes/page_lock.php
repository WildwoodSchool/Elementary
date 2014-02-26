<?php

$today = date("Y-m-d");

if(($today >= $prefarray['setting_lock_date']) && ($prefarray['setting_lock_override'] != 1) && ( strpos($user[flags], "a") === false)){
//	header ("Location: /elementary/extras/locked.php");
echo "	<style type='text/css'>
	#overlay {
		position: absolute;
		background-color: #fff;
		opacity: .80;
		filter: alpha(opacity=80);										
		z-index: 1000;												
		display: inline;												
		width: 100%;
		height: 100%;
		overflow: hidden;
	}
	body {
		overflow: hidden;
	} 
	</style>

	<div style='position:absolute;top:150px;width:100%;opacity:1;z-index: 1001;font-size:20px'>
	<center><img src='../imgs/Hand.png'><br>Sorry ".$user[fname].", the page you requested is currently closed.<br><br>
	<small>Please use your browser's back button to return to your previous page</small></center>
		</div><div id='overlay'>
	</div>";

}
else{
//	echo "<div style='position:fixed;bottom:5;right:5;color:#bbbbbb'>Please note: This section will be <br>closed to editing on ".date("m/d/Y", strtotime($prefarray['setting_lock_date']))."</div>";
}
?>