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
}
else{
	$grade = $_GET['grade'];
	
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

// If submitted, get POST data
if(isset($_POST['submit'])){
	
	// Create key
	$subjkey = "subj".$subjectid."_grade".$grade;
	
	// Get and update notes and comments
	$description_1 = mysql_real_escape_string($_POST['description_1']);
	$description_2 = mysql_real_escape_string($_POST['description_2']);
	
	$descripsql = "INSERT INTO elem_subject_descriptions (descripid, subjectid, grade, description_1, description_2) VALUES ('$subjkey', '$subjectid', '$grade', '$description_1', '$description_2')
	ON DUPLICATE KEY UPDATE description_1 = '$description_1', description_2 = '$description_2';";
 	mysql_query($descripsql) or die (mysql_error());
	
// 	if($grade == 0){
// 		$grade = 1;
// 		$subjkey = "subj".$subjectid."_grade".$grade;
// 		$descripsql = "INSERT INTO elem_subject_descriptions (descripid, subjectid, grade, description_1, description_2) VALUES ('$subjkey', '$subjectid', '$grade', '$description_1', '$description_2')
// 		ON DUPLICATE KEY UPDATE description_1 = '$description_1', description_2 = '$description_2';";
// 		mysql_query($descripsql) or die (mysql_error());
//  	}
	
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
    <script type="text/javascript" src="/elementary/js/popitup.js"></script>

    <link rel="stylesheet" type="text/css" media="all"
          href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"    />
	<script type="text/javascript">
		tinyMCE.init({
			content_css : "/css/tiny_mce_custom_content.css",
			theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px",
			font_size_style_values : "10px,12px,13px,14px,16px,18px,20px",
			mode : "textareas",
// 			elements : "elm1",
			theme : "advanced",
			plugins : "tinyautosave,autosave,spellchecker,paste",
			theme_advanced_buttons1 : "bold,underline,italic,bullist,numlist,spellchecker,autosave",
			theme_advanced_buttons3 : "",
			spellchecker_languages : "+English=en,Spanish=es",
			valid_styles : {'*' : 'color,font-size,font-weight,font-style,text-decoration'},
			paste_use_dialog : true,
			paste_auto_cleanup_on_paste : true,
			theme_advanced_buttons1_add: "tinyautosave"
		});
	
		$(window).load(function(){
			$('input[type="checkbox"]').change(function() {
				var val = "<b>" + this.value + "</b>";
				if (this.checked) {
					tinyMCE.get('elm1').setContent(tinyMCE.get('elm1').getContent() + val)
				}
				else {
					tinyMCE.get('elm1').setContent(tinyMCE.get('elm1').getContent().replace(val, ''))
				}
			});
		});
	
	</script>
</head>
<body>

<div id="wrapper">
	<div id="header">
		<div id="userPanel"></div>

<img src="/images/assessmentsLogo.png" style="padding:10px 20px 30px 20px;">

<?php

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
<a href='javascript:void(0);' onclick='return popitup(\"http://connections.wildwood.org/elementary/admin/preview.php?grade=".$grade."&sem=1 \" )'><b>Winter</b></a> | 
<a href='javascript:void(0);' onclick='return popitup(\"http://connections.wildwood.org/elementary/admin/preview.php?grade=".$grade."&sem=2 \")'><b>Spring</b></a></div>";


// Start subnav
echo "<h3>Course Description: ".$elem_divisions[$grade]." ".$subj_name_array[$subjectid]."</h3><ul id='subnav' style='margin-left:0px'>";

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

// Get subject info by grade
$coursesql = "SELECT * FROM elem_subject_descriptions WHERE subjectid = '$subjectid' AND grade = '$grade'";
$courseresult = mysql_query($coursesql) or die (mysql_error());
$courserow = mysql_fetch_array($courseresult);

// Echo description inputs 
echo "</ul><br><br><form name='subjinfo' method='POST'>";

foreach($semesters as $semester => $semname){
	$description = stripslashes($courserow['description_'.$semester]);
	echo "<h3>".$semname." Semester:</h3><textarea name='description_".$semester."' rows='15' style='width:95%'>".$description."</textarea><br>";
}
		echo "<h3><input name='submit' type='submit' style='margin-right:30px;float:right;font-size:100%' value='Save Section'></h3></form>";

?>
</div>
<div style="padding-bottom:50px"></div>
<?php

