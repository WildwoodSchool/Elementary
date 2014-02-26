<?php

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info] 
require ("../includes/usercheck.php");

// Connect to elementary DB
require ("../includes/database.php");

// Get Array Values
require ("../includes/arrays.php");


// Failed attempt at using Zend auth. QQ
// 	$auth = Zend_Auth::getInstance();
// 	$username = str_replace("WILDWOOD\\", "",$auth->getIdentity());
//  echo $username;



if(!isset($_GET['grade'])){
	$grade = 0;
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

echo $_POST['headingspost'];
$headingspost = explode(",",$_POST['headingspost']);
foreach($headingspost as $num){
		$headingnumb = "table-".$num."-return";
		$headingarray = $_POST[$headingnumb];
		echo $headingarray;
		}

// 	$stuid = $_GET['stuid'];
// 	$skillspost = $_POST['skillspost'];
// 
// 	// Turn skillspost back into array
// 	$skillspost = explode(",",$skillspost);
// 
// 	// Loop through array of skills getting each value and updating/inserting into table
// 	foreach($skillspost as $num){
// 		$skillnumb = "skill_".$num;
// 		$stuskillval = $_POST[$skillnumb];
// 		$skillkey = $stuid."_s".$_SESSION['semester']."_skill".$num;
// 		$thesql = "INSERT INTO elem_stu_skills (stu_assessment_key, assessid, stuid, skillid, semester) VALUES ('$skillkey', '$stuskillval', '$stuid', '$num', '".$_SESSION['semester']."')
// 		ON DUPLICATE KEY UPDATE assessid = '$stuskillval';";
//  		mysql_query($thesql) or die (mysql_error());
// 	}
// 	
// 	// Get and update notes and comments
// 	$newcomments = mysql_real_escape_string($_POST['comments']);
// 	$newnotes = mysql_real_escape_string($_POST['notes']);
// 	$subjkey = $stuid."_s".$_SESSION['semester']."_subj".$subjectid;
// 	$notekey = $stuid."_note".$subjectid;
// 
// 	$notesql = "INSERT INTO elem_stu_notes (noteid, subjectid, stuid, notes) VALUES ('$notekey', '".$subjectid."', '$stuid', '$newnotes')
// 	ON DUPLICATE KEY UPDATE notes = '$newnotes';";
//  	mysql_query($notesql) or die (mysql_error());
// 	
// 	$commentsql = "INSERT INTO elem_stu_comments (commentid, subjectid, stuid, comments, semester) VALUES ('$subjkey', '".$subjectid."', '$stuid', '$newcomments', '".$_SESSION['semester']."')
// 	ON DUPLICATE KEY UPDATE comments = '$newcomments';";
//  	mysql_query($commentsql) or die (mysql_error());
//  	
//  	// Get and update status
//  	$newstatus = $_POST['status'];
// 	$newstatsql = "INSERT INTO elem_stu_status (statusid, subjectid, stuid, status, semester) VALUES ('$subjkey', '".$subjectid."', '$stuid', '$newstatus', '".$_SESSION['semester']."')
// 	ON DUPLICATE KEY UPDATE status = '$newstatus';";
//  	mysql_query($newstatsql) or die (mysql_error()); 	

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
    <script type="text/javascript" src="/elementary/js/jquery.tablednd.js"></script>
    <link rel="stylesheet" type="text/css" media="all"
          href="/elementary/js/tablednd/tablednd.css"    />

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
<?php
// Create drag and drop for all tables
echo "<script type=\"text/javascript\">
	$(document).ready(function() {";
$headingsql = "SELECT * FROM elem_headings WHERE subjectid = '$subjectid' OR subjectid = '0' ORDER BY heading_sort ASC";
$headingresult = mysql_query($headingsql) or die (mysql_error());
while($headingrow = mysql_fetch_array($headingresult)){
	$tablenum = $headingrow['headingid'];
// 	echo "$(\"#table-".$tablenum."\").tableDnD();
// 	$(\"#table-".$tablenum." tr:even').addClass('alt')\");
// 	";
	echo "
	$(\"#table-".$tablenum."\").tableDnD({
		onDragClass: \"myDragClass\",
        onDrop: function(table, row) {
        var debugStr = '';
 		var rows = table.tBodies[1].rows;
 			debugStr = rows[0].id ;
            for (var i=1; i<rows.length; i++) {
                 debugStr += ',' + rows[i].id;
            }
       	 document.getElementById('table-".$tablenum."-return').value = debugStr;
		}
	});";
}
echo " });
</script>";
?>
</head>
<body>

<div id="wrapper">
	<div id="header">
		<div id="userPanel"></div>
		<div id="searchFloat">
			<input type="text" size="20" onkeyup="showResult(this.value)" id="searchInput" style="width:120px;" />
			<div id="livesearch"></div>
		</div>
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
echo "</ul><br><br><form method='post'>";


// Create array of all skills on page
$postdata = array();

// Show headings for selected subject
$headingsql = "SELECT * FROM elem_headings WHERE subjectid = '$subjectid' OR subjectid = '0' ORDER BY heading_sort ASC";
$headingresult = mysql_query($headingsql) or die (mysql_error());
while($headingrow = mysql_fetch_array($headingresult)){

	// Add the headingid to the post array
	$postdata[] = $headingrow['headingid'];

	
	// Echo heading
	echo "<h3>".$headingrow['heading']."</h3><table id='table-".$headingrow['headingid']."' width='95%' class='table' ><th align='left'>&nbsp;&nbsp;Visibility</th><th>Description</th><tbody>";			
	
	// Then skills for that heading
	$skillsql = "SELECT * FROM elem_skills WHERE headingid = '".$headingrow['headingid']."' AND grade = '$grade'  ORDER BY semester ASC, skill_sort ASC";
	$skillresult = mysql_query($skillsql) or die (mysql_error());
	while($skillrow = mysql_fetch_array($skillresult)){
// 		$set = $skillrow['set_id'];		  	// This would be enabled if using any assessment set other than EMAD;
		$skillid = $skillrow['skillid'];
		$skillsort = $skillrow['skill_sort'];

		$skillsemester = $skillrow['semester'];
		

		// Echo semester selection
		echo "<tr id='".$skillid ."'><td width='22%'><select name='skillsem_".$skillid."'>";
		foreach($skillsemesters as $key => $option){
			if($skillsemester == $key){
				echo "<option value='".$key."' selected='selected'>".$option."</option>";
			}
			else{
				echo "<option value='".$key."'>".$option."</option>";
			}
		}
		$skill = stripslashes($skillrow['skill']);
		echo "</select></td><td>".$skill."</td></tr>
		";
	}
	echo "</tbody></table><input type='hidden' name='table-".$headingrow['headingid']."-return' id='table-".$headingrow['headingid']."-return'><br>";
}


// Turn skill numbers array into CSV for POST
$headingspost = implode(",", $postdata);
echo "<input type='hidden' name='headingspost' value='".$headingspost."'><input type='submit' name='submit' value='submit'></form>";
	
	

?>
</div>
<div style="padding-bottom:50px"></div>
<?php

