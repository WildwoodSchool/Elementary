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
echo "<h3>Subject Descriptions: ".$elem_divisions[$grade]."</h3><ul id='subnav' style='margin-left:0px'>";

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
$coursesql = "SELECT * FROM elem_subject_descriptions WHERE subjectid = '$subjectid' AND grade = '$grade'";
$courseresult = mysql_query($coursesql) or die (mysql_error());
$courserow = mysql_fetch_array($courseresult);


echo "</ul><br><br><h3>Teachers: <input type='text' size='40' name='teachers' style='font-size:90%' value='".$courserow['teachers']."'></h3><br>";

foreach($semesters as $semester => $semname){
	$description = stripslashes($courserow['description_'.$semester]);
	echo "<h3>".$semname." Semester:</h3><textarea name='description_".$semester."' rows='15' style='width:95%'>".$description."</textarea><br>";
}

// 	
// 		// Get and display teacher and course description
// 		$coursesql = "SELECT elem_subject_descriptions.description, elem_subject_descriptions.teachers FROM elem_subject_descriptions WHERE subjectid = '$subjectid' AND grade = '$stugrade'";
// 		$courseresult = mysql_query($coursesql) or die (mysql_error());
// 		$courserow = mysql_fetch_array($courseresult);
// 		$description = stripslashes($courserow['description']);
// 		echo "<table id='table' width='95%'><tr><th align='left'>&nbsp;&nbsp;Class Description</th></tr>
// 		<tr><td><div style='overflow: auto;color:#666;width:100%;height:150px;font-family:\"lucida sans\",sans-serif;font-size:12px;' >".$description."</div></td></tr></table><br>";
// 	
// 		// Add course description to PDF
// 		$pdf_content .= "<span style='font-size: 12px'><b>Teacher(s): </b> " . $courserow['teachers']. "<br /></span><br /><b>Description:</b><br />".$description."<span style='color: #fff'>-</span><br />";
// 	
// 		// Create array of all skills on page
// 		$postdata = array();
// 
// 		// Show headings for selected subject
// 		$headingsql = "SELECT * FROM elem_headings WHERE subjectid = '$subjectid' OR subjectid = '0' ORDER BY heading_sort ASC";
// 		$headingresult = mysql_query($headingsql) or die (mysql_error());
// 		while($headingrow = mysql_fetch_array($headingresult)){
// 			$skillsql = "SELECT * FROM elem_skills WHERE headingid = '".$headingrow['headingid']."' AND grade = '$stugrade' AND semester <= '".$_SESSION['semester']."' ORDER BY semester ASC, skill_sort ASC";
// 			$skillresult = mysql_query($skillsql) or die (mysql_error());
// 	
// 			// Only echo heading if it has skills associated for given grade
// 			if((mysql_num_rows($skillresult)) > 0){
// 	
// 				// Echo heading
// 				echo "<table id='table' width='95%'><th colspan='2' align='left'>&nbsp;&nbsp;".$headingrow['heading']."</th>";			
// 		
// 				// Clear an existng skill table and create new for PDF
// 				$pdf_skill_table = "";
// 				$pdf_skill_rows = "";
// 				$pdf_skill_table = "<table width='100%'><tr><td colspan='5' class='noBorder'><b>".$headingrow['heading']."</b></td></tr>";
// 	
// 				// For each skill, first get assessment set
// 				while($skillrow = mysql_fetch_array($skillresult)){
// 					$set = $skillrow['set_id'];
// 					$skillid = $skillrow['skillid'];
// 		
// 					// Add the skill to the post array
// 					$postdata[] = $skillid;
// 					
// 					// Clear PDF rows
// 					$pdf_toprow_abbrs = "";
// 					$pdf_assess_row = "";
// 					$pdf_row_color = "";
// 						
// 					// Echo assessment selection
// 					echo "<tr><td width='20%'><select name='skill_".$skillid."'>";
// 
// 					// For skills only to be displayed
// 					if(($_SESSION['semester'] ==  1) && ($skillrow['semester'] == 1)){
// 						echo "<option value='spr'>Spring Semester</option>";
// // 						$pdf_assess_row .= "<td colspan='4'><center>Spring Semester</center></td>";
// 						$pdf_assess_row .= "<td></td><td></td><td></td><td></td>";
// 
// 						$pdf_row_color = "bgcolor='#BDECEB'";
// 
// 						// Still need to create top row for PDF
// 						$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '$set' ORDER BY assess_sort DESC";
// 						$assessresult = mysql_query($assesssql) or die (mysql_error());
// 						while($assessrow = mysql_fetch_array($assessresult)){
// 							$pdf_toprow_abbrs .= "<td width='30'><center>".$assessrow['assess_abbr']."</center></td>";  	
// 						}
// 					}
// 					
// 					
// 					// Display the skill with assessment options
// 					else{
// 					$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '$set' ORDER BY assess_sort DESC";
// 					$assessresult = mysql_query($assesssql) or die (mysql_error());
// 					while($assessrow = mysql_fetch_array($assessresult)){
// 				
// 						// Create top row for PDF
// 						$pdf_toprow_abbrs .= "<td width='30'><center>".$assessrow['assess_abbr']."</center></td>";  
// 						
// 							// Set selected value in assessment select menu
// 	
// 							// First check if skill has been assessed before
// 							$stuskillsql = "SELECT * FROM elem_stu_skills WHERE skillid = '".$skillid."' AND stuid = '".$_GET['stuid']."' AND semester = '".$_SESSION['semester']."'";
// 							$stuskillresult = mysql_query($stuskillsql) or die (mysql_error());				
// 							$stuskillrow = mysql_fetch_assoc($stuskillresult);
// 							$stuassessment = $stuskillrow['assessid'];
// 
// 							if((mysql_num_rows($stuskillresult) > 0) && ($stuskillrow['assessid'] != "spr") ){								
// 								if($stuassessment == $assessrow['assessid']){
// 									echo "<option value = '".$assessrow['assessid']."' selected='selected'>".$assessrow['assess_name']."</option>";
// 									$pdf_assess_row .= "<td><center>X</center></td>";
// 								}
// 								else{
// 									echo "<option value = '".$assessrow['assessid']."'>".$assessrow['assess_name']."</option>";
// 									$pdf_assess_row .= "<td></td>";
// 								}
// 							}
// 							
// 							// If skill hasn't been assessed, set to default
// 							else{
// 									if($assessrow['deft'] == 1){
// 										echo "<option value = '".$assessrow['assessid']."' selected='selected'>".$assessrow['assess_name']."</option>";
// 									}
// 									else{
// 										echo "<option value = '".$assessrow['assessid']."'>".$assessrow['assess_name']."</option>";								
// 									}
// 							// Add blank boxes to PDF for unassessed skills
// 							$pdf_assess_row .= "<td></td>";	
// 							}
// 						}
// 					}
// 					$skill = stripslashes($skillrow['skill']);
// 					echo "</select></td><td>".$skill."</td></tr>";
// 					$pdf_skill_rows .= "<tr ".$pdf_row_color.">".$pdf_assess_row."<td>".(rtrim($skill, '.'))."</td></tr>
// 					";
// 				}
// 				$pdf_skill_table .= "<tr>".$pdf_toprow_abbrs."<td>Skills and Content:</td></tr>
// 				".$pdf_skill_rows."</table><span style='color: #fff'>-</span><br />
// 				";
// 				$pdf_content .= $pdf_skill_table;
// 				
// 				// Turn skill numbers array into CSV for POST
// 				$skillspost = implode(",", $postdata);
// 				echo "</table><br><input type='hidden' name='skillspost' value='".$skillspost."'>";
// 			}	
// 		}
// 				
// 		// Get comments
// 		$commentsql = "SELECT elem_stu_comments.comments FROM elem_stu_comments WHERE stuid = '".$_GET['stuid']."' AND subjectid = '$subjectid' AND semester = '".$_SESSION['semester']."'";
// 		$commentresult = mysql_query($commentsql) or die (mysql_error());
// 		$commentrow = mysql_fetch_array($commentresult);
// 		$comments = stripslashes($commentrow['comments']);
// 		$pdf_content .= "<b>Comments</b><br />
// 		".$comments."</body></html>";
// 	
// 		// Get notes
// 		$notesql = "SELECT elem_stu_notes.notes FROM elem_stu_notes WHERE stuid = '".$_GET['stuid']."' AND subjectid = '$subjectid'";
// 		$noteresult = mysql_query($notesql) or die (mysql_error());
// 		$noterow = mysql_fetch_array($noteresult);
// 		$notes = stripslashes($noterow['notes']);
// 	
// 		// Display comments and notes fields	
// 		echo "<table id='table' width='95%' ><tr><th align='left'>&nbsp;&nbsp;Student Comments (will be printed on student's progress report)</th></tr></table><textarea id='elm1' name='comments' rows='14' style='color:#222;width:95%;font-family:\"lucida sans\",sans-serif;'>".htmlentities($comments)."</textarea>
// 		<br><table id='table' width='95%' ><tr><th align='left'>&nbsp;&nbsp;Notes (only visible internally)</th></tr><tr><td><textarea name='notes' rows='10' style='font-size:14px;border:none;color:#222;width:100%;font-family:\"lucida sans\",sans-serif;'>".$notes."</textarea></td></tr></table>
// 		<br><br><h3>Status: ";
// 		
// 		// Set status menu		
// 		$statselect = "<select name='status' style='font-size:95%'>";
// 		foreach($statusvalues as $key => $statusname){
// 			if($key == $currentstatus){
// 				$statselect .= "<option value='".$key."' selected='selected'>".$statusname."</option>";
// 			}
// 			else{
// 				$statselect .= "<option value='".$key."'>".$statusname."</option>";
// 			}	
// 		}
// 		$statselect .= "</select>";
// 		echo $statselect;
// 		echo "<input name='submit' type='submit' style='margin-right:30px;float:right;font-size:100%' value='Save Section'></h3></form>";
// 	}		
// }
?>
</div>
<div style="padding-bottom:50px"></div>
<?php

