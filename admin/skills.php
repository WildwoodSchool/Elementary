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



if(!isset($_GET['grade'])){
	$grade = 1;
	$skillgrade = 1;
}

else{
	$grade = $_GET['grade'];
	$skillgrade = $grade;
	if ($grade == 1){
		$skillgrade = 1;
	}
}


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
if(isset($_POST['addskill'])){
	$newskillskill = mysql_real_escape_string($_POST['newskillskill']);
	$newskillheading = mysql_real_escape_string($_POST['newskillheading']);
	$newskillsem = mysql_real_escape_string($_POST['newskillsem']);
	$newsql = "INSERT INTO elem_skills (skill, grade, subjectid, headingid, set_id, semester) VALUES ('$newskillskill', '$skillgrade', '$subjectid', '$newskillheading', 1, '$newskillsem')";
	mysql_query($newsql) or die (mysql_error());
}

// If submitted, get POST data
if(isset($_POST['submit'])){

// 	echo $_POST['table-1-return']."<br>";
	$headingspost = explode(",",$_POST['table-1-return']);
	foreach($headingspost as $skillid){
		if((substr($skillid, 0, 3)) == "hid"){
			$newhead = str_replace("hid_","",$skillid);
			$newsort = 0;
		}
		else{
			$newsort = $newsort + 1;
			$newsem = mysql_real_escape_string($_POST["skillsem_".$skillid]);
			$newdesc = mysql_real_escape_string($_POST["des_".$skillid]);
			$delete = $_POST["delete_".$skillid]; 
			if($delete == 1){
				$skillsql = "DELETE FROM elem_skills WHERE skillid = '$skillid'";
			}
			else{
				$skillsql = "UPDATE elem_skills SET skill_sort = '$newsort', headingid = '$newhead', semester = '$newsem', skill='$newdesc' WHERE skillid = '$skillid'";
			}
			mysql_query($skillsql) or die (mysql_error());
		}
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
    <script type="text/javascript" src="/elementary/js/popitup.js"></script>
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
			var debugStr = table.tBodies[0].rows[0].id;
			var rows = table.tBodies[1].rows;

				for (var i=0; i<rows.length; i++) {
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
	echo "<div name='saved' id='saved'><br><center>Skills Saved</center></div>";
}

// Echo Navbar
include ("../includes/navbar.php");

?>

    <div id="content">
    <div id="aside" style="margin-right:20px; height: 48px;"><ul >

<?php   


// Create grade list
foreach($elem_divisions as $key =>$gradename){
	if($key == $grade){
		echo "<li id='active'>".$gradename."</li>";
	}
	else{
		echo "<a href='?subj=".$subjectid."&grade=".$key."'><li>".$gradename."</li></a>";
	}
}

echo "</ul></div><div style='margin-left:220px'>";

// Open Preview
echo "<div style='float:right; width:150px; text-align:right; margin-right:40px'>
<a href='javascript:void(0);' onclick='return popitup(\"http://connections.wildwood.org/elementary/admin/preview.php?grade=".$grade."&sem=".$_SESSION['semester']." \")'><img src='/elementary/imgs/preview.png' height='36px' style='float:right;margin-left:5px'></a>
Preview Template<br>
<a href='javascript:void(0);' onclick='return popitup(\"http://connections.wildwood.org/elementary/admin/preview.php?grade=".$grade."&sem=1 \")'><b>Winter</b></a> | 
<a href='javascript:void(0);' onclick='return popitup(\"http://connections.wildwood.org/elementary/admin/preview.php?grade=".$grade."&sem=2 \")'><b>Spring</b></a></div>";


// Start subnav
echo "<h3>Skill Descriptions: ".$elem_divisions[$grade]."</h3><ul id='subnav' style='margin-left:0px'>";

// Get subjects in subnav
$subjsql = "SELECT * FROM elem_subjects ORDER BY subject_name ASC";
$subjresult = mysql_query($subjsql) or die (mysql_error());
while($subjrow = mysql_fetch_array($subjresult)){

	// Make selected subject current, set currentstatus value, and display subjects
	if($subjrow['subjectid'] == $subjectid){
		$subjectname = $subjrow['subjec_name'];
		echo "<li class='current'><a href='?subj=".$subjrow['subjectid']."&grade=".$grade."''>".$subjrow['subject_abbr']."</a></li>";
	}
	else{
		echo "<li><a href='?subj=".$subjrow['subjectid']."&grade=".$grade."''>".$subjrow['subject_abbr']."</a></li>";
	}
}
echo "</ul><br><h3>Add New Skill

</h3><form name='newskillform' method='POST'><table width='100%'><tr><td width='80%'><span style='font-size:110%'>Heading: ";

//Create heading select menu
$headingsql = "SELECT * FROM elem_headings WHERE subjectid = '$subjectid' OR subjectid = '0' ORDER BY heading_sort ASC";
$headingresult = mysql_query($headingsql) or die (mysql_error());
$headingmenu = "<select name='newskillheading' >";
while($headingrow = mysql_fetch_array($headingresult)){
	$headingmenu .= "<option value='".$headingrow['headingid']."'>".$headingrow['heading']."</option>";
}
$headingmenu .= "</select>";
$headingmenu = str_replace("<option value='".$_POST['newskillheading']."'>","<option value='".$_POST['newskillheading']."' selected='selected'>",$headingmenu);
echo $headingmenu;
echo "</span></td><td></td></tr>
<tr><td><span style='font-size:110%'>Semester: <select name='newskillsem'>";
foreach($skillsemesters as $key => $option){
	echo "<option value='".$key."'>".$option."</option>";
}
echo "</select></span></td></tr><tr><td><textarea rows='3' name='newskillskill' style='font-family:\"lucida sans\",sans-serif;font-size:12px;background-color:transparent;border:1px solid #aaaaaa;width:100%;resize:none;padding:2px;'></textarea></td><td><h3><input name='addskill' type='submit' style='margin-right:30px;float:right;font-size:100%' value='Add Skill'></h3></td></tr></table></form></span>

<br>
<hr>
<form name='skills' method='post'>";

// User info box
echo "<br><h3>Edit Skills</h3><img src='/elementary/imgs/info.png' width='35px' height='35px' style='float:left'><i>Skills are specific to each grade. To edit skills, simply click in the box and type. Skills will print in the order listed and can be rearranged by grabbing the edge of the box and dragging--even into different headings (Note: headings without skills will not be printed). To delete a skill, click the box on the right.
<b>Make sure to save your changes when done.</b></i>";

// top save button
//echo "<input name='submit' type='submit' style='margin-right:30px;float:right;font-size:100%' value='Save Skills' onclick='changeFunc(false);'></h3>";

// Create table and input field for skill sorting
echo "<input type='hidden' size='90' name='table-1-return' id='table-1-return'>
<table id='table-1' name='table-1' width='95%'  class='table' style='border:#fff;'>";

$headcount = 0;

// Show headings for selected subject
$headingsql = "SELECT * FROM elem_headings WHERE subjectid = '$subjectid' OR subjectid = '0' ORDER BY heading_sort ASC";
$headingresult = mysql_query($headingsql) or die (mysql_error());
while($headingrow = mysql_fetch_array($headingresult)){

	// Add the headingid to the post array

	$headcount = $headcount + 1;
	if($headcount == 1){
	// Echo heading
		echo "<tr id='hid_".$headingrow['headingid']."' class='nodrag' style='color:#000000; background-color:#ffffff;font-size:1.1em;font-weight:bold;border:#fff'><td colspan='2' width='96%'><br>".$headingrow['heading']."</td><td><br>Delete</td></tr><tbody>";			
	}
	else{
		echo "<tr id='hid_".$headingrow['headingid']."' class='nodrag' style='color:#000000; background-color:#ffffff;font-size:1.1em;font-weight:bold;border:#fff'><td colspan='3'><br><br>".$headingrow['heading']."</td></tr>";			
	}
	// Then skills for that heading
	$skillsql = "SELECT * FROM elem_skills WHERE headingid = '".$headingrow['headingid']."' AND subjectid = '$subjectid' AND grade = '$grade'  ORDER BY skill_sort ASC";
	$skillresult = mysql_query($skillsql) or die (mysql_error());
	while($skillrow = mysql_fetch_array($skillresult)){
// 		$set = $skillrow['set_id'];		  	// This would be enabled if using any assessment set other than EMAD;
		$skillid = $skillrow['skillid'];
		$skillsort = $skillrow['skill_sort'];
		$skillsemester = $skillrow['semester'];
		
		// Echo semester selection
		echo "<tr id='".$skillid ."'><td width='25%'><select name='skillsem_".$skillid."'>";
		foreach($skillsemesters as $key => $option){
			if($skillsemester == $key){
				echo "<option value='".$key."' selected='selected'>".$option."</option>";
			}
			else{
				echo "<option value='".$key."'>".$option."</option>";
			}
		}
		$skill = stripslashes($skillrow['skill']);
// 		echo "</select></td><td><input class='clearish' name='des_".$skillid."' value='".$skill."'></td></tr>
// 		";
		echo "</select></td><td width='72%'><textarea rows='2' name='des_".$skillid."' style='font-family:\"lucida sans\",sans-serif;font-size:12px;background-color:transparent;border:transparent;outline:none;width:100%;resize:none'  onchange='changeFunc(true);'>".$skill."</textarea></td>
		<td width='4%' align='center'><input name='delete_".$skillid."' type='checkbox' value='1' onchange='changeFunc(true);'></td></tr>
		";
	}
}
echo "</tbody></table>";
echo "<h3><input name='submit' type='submit' style='margin-right:30px;float:right;font-size:100%' value='Save Skills'  onclick='changeFunc(false);'></h3></form>";

?>
</div>

<script type="text/javascript">
	table = document.getElementById("table-1");
	var trs = table.getElementsByTagName("tr");
	var debugStr = trs[0].id;
	for (var i=1; i<trs.length; i++) {
		 debugStr += "," + trs[i].id;
	}
	 document.getElementById('table-1-return').value = debugStr;

</script>

<div style="padding-bottom:50px"></div>
<?php

