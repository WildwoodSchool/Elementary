<?php

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info] 
require ("../includes/usercheck.php");

// Connect to elementary DB
require ("../includes/database.php");

// Get Array Values
require ("../includes/arrays.php");

// Check if page is locked
require ("../includes/page_lock.php");

// Failed attempt at using Zend auth. QQ
// 	$auth = Zend_Auth::getInstance();
// 	$username = str_replace("WILDWOOD\\", "",$auth->getIdentity());
//  echo $username;


// Default to first alphabetical subject if one isn't selected
if(!isset($_GET['subj'])){
	$firstsubj = mysql_fetch_row(mysql_query("SELECT elem_subjects.subjectid FROM elem_subjects ORDER BY subject_name ASC LIMIT 1"));
	$subjectid = $firstsubj[0];
}

// Otherwise use the selected subject	
else{
	$subjectid = $_GET['subj'];
}	

// If skill added, get POST data
if(isset($_POST['addhead'])){
	$newhead = mysql_real_escape_string($_POST['newhead']);
	$newsql = "INSERT INTO elem_headings (heading, subjectid) VALUES ('$newhead', '$subjectid')";
	mysql_query($newsql) or die (mysql_error());
}

// If submitted, get POST data
if(isset($_POST['submit'])){

// 	echo $_POST['table-1-return']."<br>";
	$headingspost = explode(",",$_POST['table-1-return']);
	$newsort = 0;
	foreach($headingspost as $headingid){
		$newsort = $newsort + 1;
		$newdesc = mysql_real_escape_string($_POST["des_".$headingid]);
		$delete = $_POST["delete_".$headingid]; 
		if($delete == 1){
			$headingsql = "DELETE FROM elem_headings WHERE headingid = '$headingid'";
		}
		else{
			$headingsql = "UPDATE elem_headings SET heading_sort = '$newsort', heading='$newdesc' WHERE headingid = '$headingid'";
		}
		mysql_query($headingsql) or die (mysql_error());
		
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
    <script type="text/javascript" src="/elementary/js/jquery.tablednd.js"></script>
    <link rel="stylesheet" type="text/css" media="all"
          href="/elementary/css/tablednd.css"    />
    <link rel="stylesheet" type="text/css" media="all"
          href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"    />

	<script type="text/javascript">
	$(document).ready(function() {
		$("input:checkbox").click(function() {
			if ($(this).is(':checked')) {
				$(this).closest('tr').addClass("highlight");
			} else {
				$(this).closest('tr').removeClass("highlight");
			}
		});


 		$("#saved").fadeOut(4000);

		// Initialise the table
		$("#table-1").tableDnD({
			onDragClass: "myDragClass",
			onDrop: function(table, row) {
			changeFunc(true);
			var debugStr = table.tBodies[1].rows[0].id;
			var rows = table.tBodies[1].rows;

				for (var i=1; i<rows.length; i++) {
					 debugStr += "," + rows[i].id;
				}
			 document.getElementById('table-1-return').value = debugStr;
			}
		});
	});
	</script>
	
	<script type="text/javascript">
		var changes = false;
		function changeFunc(changes){
			if (changes == true){
				function closeEditorWarning(){
					return 'Are you sure you want to leave? You have unsaved changes.'
				}
				window.onbeforeunload = closeEditorWarning;
			}
			else{
				window.onbeforeunload = null;
			}
		console.log (changes);
		}

	</script>
		
</head>
<body>

<div id="wrapper">
	<div id="header">
		<div id="userPanel"></div>

<img src="/images/assessmentsLogo.png" style="padding:10px 20px 30px 20px;">

<?php

if(isset($_POST['submit'])){
	echo "<div name='saved' id='saved'><br><center>Saved</center></div>";
}

// Echo Navbar
include ("../includes/navbar.php");

?>

    <div id="content">

<?php   



echo "<div style='margin-left:30px'>";

// Start subnav
echo "<h3>Skill Headings</h3><ul id='subnav' style='margin-left:0px'>";

// Get subjects in subnav
$subjsql = "SELECT * FROM elem_subjects ORDER BY subject_name ASC";
$subjresult = mysql_query($subjsql) or die (mysql_error());
while($subjrow = mysql_fetch_array($subjresult)){

	// Make selected subject current, set currentstatus value, and display subjects
	if($subjrow['subjectid'] == $subjectid){
		$subjectname = $subjrow['subject_name'];
		echo "<li class='current'><a href='?subj=".$subjrow['subjectid']."'>".$subjrow['subject_name']."</a></li>";
	}
	else{
		echo "<li><a href='?subj=".$subjrow['subjectid']."'>".$subjrow['subject_name']."</a></li>";
	}
}
echo "</ul><br><br>
<h3>Add New Heading</h3><form name='newheadform' method='POST'><table width='100%'>
<tr><td width='80%'><textarea rows='1' name='newhead' style='font-family:\"lucida sans\",sans-serif;font-size:14px;background-color:transparent;border:1px solid #aaaaaa;width:100%;resize:none;padding:2px;'></textarea></td><td ><h3><input name='addhead' type='submit' style='font-size:100%' value='Add Heading'></h3></td></tr></table></form></span>
<br><hr>
<form name='heads' method='post'>";

// User info box
echo "<br><h3>Edit Headings</h3><br><img src='/elementary/imgs/info.png' width='35px' height='35px' style='float:left'><i>Skill headings are available for use in all grades. To edit headings, simply click in the box and type. Headings can be rearranged by grabbing the edge of the box and dragging. To delete a heading, click the box on the right (Note: If a heading is currently in use for 1 or more skills under Skill Descriptions, it cannot be deleted).
<b>Make sure to save your changes when done.</b></i><br>";

// top save button
//echo "<input name='submit' type='submit' style='margin-right:30px;float:right;font-size:100%' value='Save Headings' onclick='changeFunc(false);'></h3>";

// First get universal (subjectid = 0) headings
echo "<input type='hidden' size='90' name='table-1-return' id='table-1-return'>
<table width='95%'  class='table' style='border:#fff;'>
<tr class='nodrag' style='color:#000000; background-color:#ffffff;font-size:1.1em;font-weight:bold;border:#fff'><td width='96%' colspan='2'><br>Global</td></tr>";			

$headingsql = "SELECT * FROM elem_headings WHERE subjectid = '0' ORDER BY heading_sort ASC";
$headingresult = mysql_query($headingsql) or die (mysql_error());
while($headingrow = mysql_fetch_array($headingresult)){
	$heading = stripslashes($headingrow['heading']);
	echo "<tr class='nodrag'><td width='96%' height='31px' valign='top' style='font-family:\"lucida sans\",sans-serif;font-size:14px;'>".$heading."</td><td></td></tr>";
}
echo "<table id='table-1' name='table-1' width='95%'  class='table' style='border:#fff;'>
<tr class='nodrag' style='color:#000000; background-color:#ffffff;font-size:1.1em;font-weight:bold;border:#fff'><td width='96%'><br>".$subjectname."</td><td><br>Delete</td></tr><tbody>";			


// Show headings for selected subject
$headingsql = "SELECT * FROM elem_headings WHERE subjectid = '$subjectid' ORDER BY heading_sort ASC";
$headingresult = mysql_query($headingsql) or die (mysql_error());
while($headingrow = mysql_fetch_array($headingresult)){
	$heading = stripslashes($headingrow['heading']);
	echo "<tr id='".$headingrow['headingid']."'><td width='96%' ><textarea rows='1' name='des_".$headingrow['headingid']."' style='font-family:\"lucida sans\",sans-serif;font-size:14px;background-color:transparent;border:transparent;outline:none;width:100%;resize:none'  onchange='changeFunc(true);'>".$heading."</textarea></td>";
	// Check for skills for that heading
	$skillsql = "SELECT * FROM elem_skills WHERE headingid = '".$headingrow['headingid']."' AND subjectid = '$subjectid'";
	$skillresult = mysql_query($skillsql) or die (mysql_error());
	if(mysql_num_rows($skillresult) == 0){
		echo "<td width='4%' align='center'><input name='delete_".$headingrow['headingid']."' type='checkbox' value='1' onchange='changeFunc(true);'></td></tr>";
	}	
	else{
		echo "<td width='4%' align='center'></td></tr>";
	}
}
echo "</tbody></table>";
echo "<h3><input name='submit' type='submit' style='margin-right:30px;float:right;font-size:100%' value='Save Headings'  onclick='changeFunc(false);'></h3></form>";

?>
</div>

<script type="text/javascript">
	table = document.getElementById("table-1");
	var trs = table.getElementsByTagName("tr");
	var debugStr = trs[1].id;
	for (var i=2; i<trs.length; i++) {
		 debugStr += "," + trs[i].id;
	}
	 document.getElementById('table-1-return').value = debugStr;

</script>

<div style="padding-bottom:50px"></div>
<?php

