<?php

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info]; 
require "includes/usercheck.php";

// Connect to elementary DB
require "includes/database.php";

// Get Array Values
include "includes/arrays.php";


// If submitted, get POST data
if(isset($_POST['submit'])){
	$newteachernames = mysql_real_escape_string($_POST['teachername']);
	$checksql = " SELECT 1 FROM elem_teacher_prefs WHERE employeeID = '".$user[id]."'";
	$checkresult = mysql_query($checksql) or die (mysql_error());

	if(mysql_num_rows($checkresult) > 0){
		$newprefssql = "UPDATE elem_teacher_prefs SET teachernames = '$newteachernames' WHERE employeeID = '".$user[id]."'";
		$newprefsresult = mysql_query($newprefssql) or die (mysql_error());
	}
	else{
		$newprefssql = "INSERT INTO elem_teacher_prefs (teachernames, employeeID) VALUES ('$newteachernames','".$user[id]."')";
		$newprefsresult = mysql_query($newprefssql) or die (mysql_error());
	
	}
}		


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Wildwood School :: Online Evaluation System</title>
    <link href="/elementary/css/main.css" media="screen" rel="stylesheet" type="text/css" />    <script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui-1.8.21.custom.min.js"></script>
    <script type="text/javascript" src="/js/ajax.js"></script>
    <script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
    <link rel="stylesheet" type="text/css" media="all"
          href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"    />

<script type="text/javascript">
	$(document).ready(function() {

	$("#saved").fadeOut(4000);
	


<?php
foreach($semesters as $semester => $semname){
	
 	echo "$('#". $semname."-toggle').click(function(){
		$('#". $semname."-list').toggle();
		$('#". $semname."-up').toggle();
		$('#". $semname."-down').toggle();
	});
	
		$('#more-show-". $semname."').click(function(){
		$('.more-". $semname."').toggle();
		$('#more-show-". $semname."').toggle();
		$('#more-hide-". $semname."').toggle();
	}); 
	
		$('#more-hide-". $semname."').click(function(){
		$('.more-". $semname."').toggle();
		$('#more-show-". $semname."').toggle();
		$('#more-hide-". $semname."').toggle();
	}); 
	";
}
?>


	  
	});
 		
	</script>

</head>
<body>

<div id="wrapper">
    <div id="header">

        <div id="userPanel">


        </div>

                <img src="/images/assessmentsLogo.png" style="padding:10px 20px 30px 20px;">
<?php


// Echo Navbar
include "includes/navbar.php";
?>
    <div id="content">
<?php  

require ("/includes/datecheck.php");

// Get semester selection, guess if not selected
// if((!isset($_SESSION['semester'])) && (!isset($_POST['semester'])) ){
// 	if(((date(n)) >= 7) || ((date(n)) < 4)){
// 		$_SESSION['semester'] = 1;
// 	}
// 	else{
// 		$_SESSION['semester'] = 2;
// 	}
// }

 
$prefssql = "SELECT elem_teacher_prefs.teachernames FROM elem_teacher_prefs WHERE employeeID = '".$user[id]."'";
$prefsresult = mysql_query($prefssql) or die (mysql_error());
$prefsrow = mysql_fetch_array($prefsresult);
$teachernames = stripslashes($prefsrow['teachernames']);

echo "<h3>Welcome ".$user[fname]." ".$user[lname]."</h3><br>
<h3>My Name:</h3>
Please enter how you would like the name(s) to appear on your progress reports: <br><br>
<form method='POST'><input type='text' size='45' name='teachername' value='".$teachernames."' style='font-size:120%'> <input type='submit' name='submit' value='Save' style='font-size:120%'></form>";

echo "<br><br><h3>My Progress Reports:</h3>
";

// Create year selection menu
$selectyear = "<form name='yearform' id='yearform' method='POST'>School Year: <select name='year' onChange=\"document.yearform.submit()\">";
foreach($yeararray as $years){
	$selectyear .= "<option value='$years'>".($years-1)."-$years</option>";
}
$selectyear .="</select></form><br>";
$selectyear = str_replace("<option value='".$_SESSION['year']."'>","<option value='".$_SESSION['year']."' selected='selected'>",$selectyear);
echo $selectyear;


$subcount = count($full_abbr_array);
$colwidth = (100/($subcount + 2));
foreach($semesters as $semester => $semname){

	if($_SESSION['semester'] != $semester){
		$display = "display:none;";
		$opp_display = "display: inline;";
	}
	else{
		$opp_display = "display:none;";
		$display = "display: inline;";
	}
 
	echo "<div id='".$semname."-toggle'><img id='".$semname."-up' src='/images/assessments/arrow-up.png' style='".$opp_display."'><img id='".$semname."-down' src='/images/assessments/arrow-down.png' style='".$display."'>&nbsp;".$semname." Semester</div>

	<div id='".$semname."-list' style='".$display."'>
	<div id='more-show-$semname'><a >&nbsp;&nbsp;&nbsp;+&nbsp<small>View Status for All Subjects</small></a></div><div id='more-hide-$semname' style='display:none'><a>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp<small>Hide Status for All Subjects</small></a></div>
	";	
		
	$listsql = "SELECT `elem_stu_comments`.`stuid`,`elem_stu_comments`.`semester`,`elem_stu_status`.`status`,`student`.`fname`,`student`.`lname`,`elem_stu_comments`.`subjectid`
		FROM `elem_stu_comments`
		INNER JOIN `elem_stu_status` ON `elem_stu_comments`.`commentid` = `elem_stu_status`.`statusid`
		INNER JOIN `student` ON `student`.`id` = `elem_stu_comments`.`stuid`
		WHERE (`elem_stu_comments`.`last_emp` LIKE '%".$user['id']."%' OR `elem_stu_comments`.`teachers` = '$teachernames') AND `elem_stu_comments`.`semester` = '$semester'
		ORDER BY  `student`.`lname` ASC, `student`.`fname` ASC";
	$listresult = mysql_query($listsql) or die (mysql_error());
	if(mysql_num_rows($listresult) == 0){
		echo "<div style='margin:10px 50px;'><b>There are no ".$semname." progress reports for ".$teachernames."</b></div>";
	}
	else{
		echo "	<table id='table' width = '95%'><tr><th width='".(2*($colwidth))."%'>Student</th>";
	
		foreach($full_abbr_array as $subabbr){
			echo "<th width='".($colwidth)."%'>".$subabbr."</th>";
		}
		echo "</tr>";	
		while($listrow = mysql_fetch_array($listresult)){
			$stuid_in = $listrow['stuid'];
			if($stuid_in != $stuid_out){
				$moresql = "SELECT `elem_stu_status`.`subjectid`,`elem_stu_status`.`status` FROM `elem_stu_status` WHERE `elem_stu_status`.`stuid` = '$stuid_out' AND `elem_stu_status`.`semester` = '$semester'";
				$moreresult = mysql_query($moresql) or die (mysql_error());
				$morearray = null;
				while($morerow =  mysql_fetch_array($moreresult)){
					$morearray[($morerow['subjectid'])] = $morerow['status'];
				}
				
				if(!empty($stu_status)){
					echo "<tr><td><a href='/elementary/assessments?stuid=".$stuid_out."&sem=$semester'>".$stuname."</a></td>";
					foreach($full_abbr_array as $subjectid => $subabbr){
						if($stu_status[$subjectid] != null){
							echo "<td align='center'><a href='/elementary/assessments?stuid=".$stuid_out."&subj=".$subjectid."&sem=$semester'>".$statusicon[($stu_status[$subjectid])]."</a></td>";
						}
						else{
							echo "<td align='center'><div class='more-$semname' style='display:none;'><a href='/elementary/assessments?stuid=".$stuid_out."&subj=".$subjectid."&sem=$semester'>".$lightstatusicon[($morearray[$subjectid])]."</a></div></td>";
						}
					}
					echo "</tr>";
					$stu_status	= null;
				}
				$stuname = $listrow['lname'].", ".$listrow['fname'];	
				$stu_status[($listrow['subjectid'])] .= $listrow['status'];		
			}
			else{
				$stu_status[($listrow['subjectid'])] .= $listrow['status'];		
			}
		$stuid_out = $listrow['stuid'];		
		echo "</tr>";
		}
						$moresql = "SELECT `elem_stu_status`.`subjectid`,`elem_stu_status`.`status` FROM `elem_stu_status` WHERE `elem_stu_status`.`stuid` = '$stuid_out' AND `elem_stu_status`.`semester` = '$semester'";
				$moreresult = mysql_query($moresql) or die (mysql_error());
				$morearray = null;
				while($morerow =  mysql_fetch_array($moreresult)){
					$morearray[($morerow['subjectid'])] = $morerow['status'];
				}

		if(!empty($stu_status)){
			echo "<tr><td><a href='/elementary/assessments?stuid=".$stuid_out."&sem=$semester'>".$stuname."</a></td>";
			foreach($full_abbr_array as $subjectid => $subabbr){
						if($stu_status[$subjectid] != null){
							echo "<td align='center'><a href='/elementary/assessments?stuid=".$stuid_out."&subj=".$subjectid."&sessionsem=$semester'>".$statusicon[($stu_status[$subjectid])]."</a></td>";
						}
						else{
							echo "<td align='center'><div class='more-$semname' style='display:none;'><a href='/elementary/assessments?stuid=".$stuid_out."&subj=".$subjectid."&sessionsem=$semester'>".$lightstatusicon[($morearray[$subjectid])]."</a></div></td>";
						}
			}
			$stu_status	= null;
	
		}
		echo "</tr></table>";
	}
	echo "</div><br>";
}

echo "<div style='display: inline-block;border:1px dotted #999999;padding:5px'>
<table><td colspan='2' align='center'><b>Status Key:</b></td>";
foreach($statusvalues as $key => $status){
	echo "<tr><td width='21px'>".$statusicon[$key]."</td><td>".$status."</td></tr>";
}
echo "</table></div>";

?>