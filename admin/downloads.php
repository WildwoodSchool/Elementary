<?php

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info]; 
require ("../includes/usercheck.php");

// Connect to elementary DB
require ("../includes/database.php");

// Get Array Values
include ("../includes/arrays.php");

// Check that user is admin
if((strpos($user[flags], "a")) === false){    
		header ("Location: /elementary/index.php");
}

// Failed attempt at using Zend auth. QQ
// 	$auth = Zend_Auth::getInstance();
// 	$username = str_replace("WILDWOOD\\", "",$auth->getIdentity());
//     echo $username;

if(isset($_POST['dlchoice'])){
	$dlchoice = $_POST['dlchoice'];
}
// echo $dlchoice;
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


	<script type='text/javascript'>
		$(document).ready(function(){
			$('#datepicker').click(function(){
				$('#datepicker').datepicker().datepicker('show');
			});
		});
	</script>
	<script type='text/javascript'>

	function loadingAjax(div_id)
	{
		$("#"+div_id).html('<center><img src="/elementary/imgs/loading.gif"><br>Please wait while your reports are packaged...</center>');
		$.ajax({
			type: "POST",
			url: '/elementary/<?php echo $dlchoice; ?>',
			success: function(msg){
				$("#"+div_id).html(msg);
			}
		});
	}
</script>

	<script type='text/javascript'>

	function popUp(url) {
		newwindow=window.open(url,'name','toolbar=0,scrollbars=0,location=0,status=no,toolbars=no,statusbar=0,menubar=0,resizable=0,width=200,height=200');
		if (window.focus) {newwindow.focus()}
		return false;
	}
</script>

<body>

<div id="wrapper">
    <div id="header">

        <div id="userPanel">


        </div>
        <img src="/images/assessmentsLogo.png" style="padding:10px 20px 30px 20px;">
<?php

// Echo Navbar
include ("../includes/navbar.php");
?>
    <div id="content">
<?php   
// Clear $_SESSION['dlarray']
if(!isset($_POST['dlselectclass'])){
	$_SESSION['dlarray'] = null;
}

$selectclass = "<select name='dlselectclass' onChange=\"document.classform.submit()\"><option value='none'>Please Select:</option><option disabled='disabled'>&nbsp;</option>";

// Add all-grade options
foreach($gradenumbers as $key =>$gradename){
	$selectclass .= "<option value='$key'>$gradename</option>";
}

// Add all and break in menu
// $selectclass .= "<option value='ALL'>All K-5</option>";
$selectclass .= "<option disabled='disabled'>&nbsp;</option>";

// Add list of elementary (campus = 1) teachers
$teachersql = "select teacher_accounts.fname,teacher_accounts.lname,teacher_accounts.id FROM teacher_accounts INNER JOIN teacher_to_class ON teacher_to_class.empID = teacher_accounts.ID WHERE teacher_accounts.campus = '1' GROUP BY teacher_accounts.fname,teacher_accounts.lname,teacher_accounts.id";
$teacherresult = mysql_query($teachersql) or die (mysql_error());
while($teacherrow = mysql_fetch_array($teacherresult)){
	$selectclass .= "<option value='".$teacherrow['id']."'>".$teacherrow['fname']."</option>";
}

$selectclass .=  "</select>";

if(isset($_POST['dlsemester'])){
	$_SESSION['dlsemester'] = $_POST['dlsemester'];
}


// If a class was already selected
if(isset($_POST['dlselectclass'])  ){
	
	$selectclass = str_replace("<option value='".$_POST['dlselectclass']."'>","<option value='".$_POST['dlselectclass']."' selected='selected'>",$selectclass); 

	// If they selected a teacher, get the students
	if(substr($_POST['dlselectclass'],0,3) == "EMP"){
		$stusql = "SELECT student.fname,student.lname,student.id,class.name FROM student
		INNER JOIN student_to_class ON student.id = student_to_class.stuid
		INNER JOIN class
		ON student_to_class.classid = class.id
		INNER JOIN teacher_to_class
		ON class.id = teacher_to_class.classid
		WHERE teacher_to_class.empid = '".$_POST['dlselectclass']."' ORDER BY lname ASC";
	}
	
	elseif(substr($_POST['dlselectclass'],0,3) == "ALL"){
		$stusql = "SELECT * FROM student WHERE grade < '6' ORDER BY lname ASC";

	}
	
	// Otherwise they selected a grade
	else{
		$stusql = "SELECT * FROM student WHERE grade = '".$_POST['dlselectclass']."' ORDER BY lname ASC";
	}	
	// Query for students for selected class
	$sturesult = mysql_query($stusql) or die (mysql_error());
	$dlnumber = mysql_num_rows($sturesult);
	$rendertime = (round(($dlnumber * 9)/60));
	$timeest = "<br><br>This will take approximately $rendertime minutes.";
// 	if($dlnumber > 40){
// 		$timeest = "<br>(This may take a while.)";
// 	}
// 	if($dlnumber > 80){
// 		$timeest = "<br><b>Caution!</b> This will take several minutes, during which time the server may become unresponsive.";
// 	}	
	if($dlnumber > 80){
		$timeest .= "<br><br><b>Caution!</b> The server may be unresponsive during this time.";
	}
	while($sturow = mysql_fetch_array($sturesult)){
		$dlarray[] = $sturow['id'];

	}
	
	$_SESSION['dlarray'] = $dlarray;
	
//	echo var_dump($_SESSION['dlarray']);

}

// Echo class selection menu
echo "<h3>Bulk Download</h3>";

// Create year selection menu
$selectyear = "<form name='yearform' id='yearform' method='POST'>Year: <select name='year' onChange=\"document.yearform.submit()\">";
foreach($yeararray as $years){
	$selectyear .= "<option value='$years'>".($years-1)."-$years</option>";
}
$selectyear .="</select></form><br>";
$selectyear = str_replace("<option value='".$_SESSION['year']."'>","<option value='".$_SESSION['year']."' selected='selected'>",$selectyear);
echo $selectyear;

echo "<form name='classform' id='classform' method='POST'> ";

$selectdl = "<table><tr><td>Download <select name='dlchoice' onChange=\"document.classform.submit()\">
<option value='download.php'>Progress Reports</option>
<option value='confsummary.php'>Conference Summaries</option>
";
$selectdl .="</select>";
$selectdl = str_replace("<option value='".$dlchoice."'>","<option value='".$dlchoice."' selected='selected'>",$selectdl);
echo $selectdl;

echo " for </td><td>".$selectclass."</td></tr>";
// Create semester selection menu
$selectsemester = "<tr><td>Choose semester: <select name='dlsemester' onChange=\"document.classform.submit()\">";
foreach($semesters as $key =>$semestername){
	$selectsemester .= "<option value='$key'>$semestername</option>";
}

$selectsemester .="</select><br>";

$selectsemester = str_replace("<option value='".$_SESSION['dlsemester']."'>","<option value='".$_SESSION['dlsemester']."' selected='selected'>",$selectsemester);
if($dlchoice == 'download.php'){
	echo $selectsemester;
	$items = 'progress reports';
}
else{
	$_SESSION['dlsemester'] = 1;
	$items = 'conference summaries';

}

if(isset($_POST['collate'])){
	$collate = "checked='checked'";
	$_SESSION['collate'] = 'true';
}
else{
	$_SESSION['collate'] = null;
}
echo "</td></tr><tr><td>Group by teacher <input type='checkbox' value='true' name='collate' $collate onChange=\"document.classform.submit()\"></td></tr></table></form>";

if((isset($_POST['dlselectclass'])) && ($_POST['dlselectclass'] != "none"  )){

	echo "<br><br><div id='loadingpane'>You have selected  ".$dlnumber." $items. Click continue to package and download these reports.".$timeest."<br>
	<br><br><input type='submit' style='margin-right:100px;float:right;font-size:100%' value='Continue...' onclick='loadingAjax(\"loadingpane \");'></div>	</form>";
}
echo "";




?>