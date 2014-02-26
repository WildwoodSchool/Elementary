<?php

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info] 
require ("../includes/usercheck.php");

// Connect to elementary DB
require ("../includes/database.php");

// Get Array Values
require ("../includes/arrays.php");

// Check that user is admin
if( ((strpos($user[flags], "a") === false)) && ((strpos($user[flags], "r") === false))){    
		header ("Location: /elementary/index.php");
}

if((!isset($_SESSION['semester'])) && (!isset($_POST['semester'])) ){
	if(((date(n)) >= 7) || ((date(n)) == 1)){
		$_SESSION['semester'] = 1;
	}
	else{
		$_SESSION['semester'] = 2;
	}
}

if((isset($_GET['grade'])) && (!isset($_GET['list']))){
	$grade = $_GET['grade'];
	$_SESSION['reader_url'] = "?grade=".$grade;
}

elseif((!isset($_GET['grade'])) && (isset($_GET['list']))){
	$list = $_GET['list'];
	$_SESSION['reader_url'] = "?list=".$list;
}
if(!isset($_SESSION['reader_url'])){
	$grade = 0;
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
include "../includes/navbar.php";
?>
    <div id="content">
    <div id="aside" style="margin-right:20px; height: 48px;">
		<ul ><li style="background: rgb(170,170,170);}" >
<?php   

// Create year selection menu
$selectyear = "<form name='yearform' id='yearform' method='POST'>Year: <select name='year' onChange=\"document.yearform.submit()\">";
foreach($yeararray as $years){
	$selectyear .= "<option value='$years'>".($years-1)."-$years</option>";
}
$selectyear .="</select></form><br>";
$selectyear = str_replace("<option value='".$_SESSION['year']."'>","<option value='".$_SESSION['year']."' selected='selected'>",$selectyear);
echo $selectyear;


// Create semester selection menu
$selectsemester = "<form name='semesterform' id='semesterform' method='POST'>Semester: <select name='semester' onChange=\"document.semesterform.submit()\">";
foreach($semesters as $key =>$semestername){
	$selectsemester .= "<option value='$key'>$semestername</option>";
}
$selectsemester .="</select></form><br>";
$selectsemester = str_replace("<option value='".$_SESSION['semester']."'>","<option value='".$_SESSION['semester']."' selected='selected'>",$selectsemester);
echo $selectsemester;
echo "</li>";

// Create grade list


foreach($gradenumbers as $key => $gradename){
	if((!isset($_GET['list'])) && ($key == $grade) ){
		echo "<li id='active'>".$gradename."</li>";
	}
	else{
		echo "<a href='?grade=".$key."'><li>".$gradename."</li></a>";
	}
}
echo "<li><br></li>";
// Add list of elementary (campus = 1) teachers
$teachersql = "select teacher_accounts.fname,teacher_accounts.lname,teacher_accounts.id FROM teacher_accounts INNER JOIN teacher_to_class ON teacher_to_class.empID = teacher_accounts.ID WHERE teacher_accounts.campus = '1' GROUP BY teacher_accounts.fname,teacher_accounts.id";
$teacherresult = mysql_query($teachersql) or die (mysql_error());
while($teacherrow = mysql_fetch_array($teacherresult)){
	if($teacherrow['id'] == $list){
		echo "<li id='active'>".$teacherrow['fname']."</li>";
	}
	else{
		echo "<a href='?list=".$teacherrow['id']."'><li>".$teacherrow['fname']."</li></a>";
		}
}

echo "</ul>";


echo "<div style='display: inline-block;border:1px dotted #999999;padding:5px;color:#333333'>
<table><td colspan='2' align='center'><b>Status Key:</b></td>";
foreach($statusvalues as $key => $status){
	echo "<tr><td width='22px'>".$statusicon[$key]."</td><td>".$status."</td></tr>";
}
echo "</table></div></div><div id='main' style='margin-left:220px'>";

echo "<h3>Welcome ".$user[fname]." ".$user[lname]."</h3><br>";


if(isset($_GET['list'])){
	$listsql = "SELECT student.fname,student.lname,student.id FROM student
	INNER JOIN student_to_class ON student.id = student_to_class.stuid
	INNER JOIN class
	ON student_to_class.classid = class.id
	INNER JOIN teacher_to_class
	ON class.id = teacher_to_class.classid
	WHERE teacher_to_class.empid = '".$list."' ORDER BY lname ASC";
}
else{
	$listsql = "SELECT `student`.`fname`,`student`.`lname`,`student`.`id` FROM `student` WHERE `student`.`grade` = '".$grade."' ORDER BY `student`.`lname`";
}

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
	<table id='table' width = '95%'><tr><th width='".(2*($colwidth))."%'>Student</th>";
	foreach($full_abbr_array as $subabbr){
		echo "<th width='".($colwidth)."%'>".$subabbr."</th>";
	}
	echo "</tr>";
	
	$listresult = mysql_query($listsql) or die (mysql_error());
	while($listrow = mysql_fetch_array($listresult)){
		$stuid = $listrow['id'];
		$fname = $listrow['fname'];
		$lname = $listrow['lname'];
		$stustatussql = "SELECT elem_stu_status.status, elem_stu_status.subjectid FROM elem_stu_status WHERE stuid = '".$stuid."' AND semester = '".$semester."'";
		$stustatusresult = mysql_query($stustatussql) or die (mysql_error());
		// Create array of all statuses for a student
		while($statusrow = mysql_fetch_array($stustatusresult)){
			$statusarray[($statusrow['subjectid'])] .= $statusrow['status'];
		}
		echo "<tr><td><a href='/elementary/assessments?stuid=".$stuid."&sessionsem=$semester'>".$lname.", ".$fname."</a></td>";
				
		foreach($full_abbr_array as $subjectid => $subabbr){
			echo "<td align='center'><a href='/elementary/assessments?stuid=".$stuid."&subj=".$subjectid."&sessionsem=$semester'>".$statusicon[($statusarray[$subjectid])]."</a></td>";
		}
		$statusarray = null;

	}
	echo "</tr></table></div><br>";
}

