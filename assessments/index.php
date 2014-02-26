<?php

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info] 
require ("../includes/usercheck.php");

// Connect to elementary DB
require ("../includes/database.php");

// Get Array Values
require ("../includes/arrays.php");

$pageurl = $_SERVER['REQUEST_URI'];

require ("../includes/datecheck.php");

// Default to general subject if one isn't selected
if(!isset($_GET['subj'])){
	$subjectid = 0;
}

// Otherwise use the selected subject	
else{
	$subjectid = $_GET['subj'];
}	

// If submitted, get POST data
if(isset($_POST['submit'])){
	$stuid = $_GET['stuid'];
	$skillspost = $_POST['skillspost'];

	// Turn skillspost back into array
	$skillspost = explode(",",$skillspost);

	// Loop through array of skills getting each value and updating/inserting into table
	foreach($skillspost as $num){
		$skillnumb = "skill_".$num;
		$stuskillval = $_POST[$skillnumb];
		$skillkey = $stuid."_s".$_SESSION['semester']."_skill".$num;
		$thesql = "INSERT INTO elem_stu_skills (stu_assessment_key, assessid, stuid, skillid, semester) VALUES ('$skillkey', '$stuskillval', '$stuid', '$num', '".$_SESSION['semester']."')
		ON DUPLICATE KEY UPDATE assessid = '$stuskillval';";
 		mysql_query($thesql) or die (mysql_error());
	}
	
	// Get and update notes and comments
	$newcomments = mysql_real_escape_string($_POST['comments']);
	$newcomments = str_replace("#fname",$_SESSION['postname'],$newcomments);

	$newnotes = mysql_real_escape_string($_POST['notes']);
	$new_reader_notes = mysql_real_escape_string($_POST['reader_notes']);
	$newteachers = mysql_real_escape_string($_POST['teachers']);
	$last_user = $user[fname]." ".$user[lname];
	$subjkey = $stuid."_s".$_SESSION['semester']."_subj".$subjectid;
	$notekey = $stuid."_note".$subjectid;
	
	// Add employee IDs to last_emp column
	$lastemps = mysql_real_escape_string($_POST['last_emps']);
	$lastemps = explode(",",$lastemps);
	$lastemps[] = $user[id];
	$lastemps = array_unique($lastemps);
	$lastemps = implode(",",$lastemps);

	$notesql = "INSERT INTO elem_stu_notes (noteid, subjectid, stuid, notes) VALUES ('$notekey', '".$subjectid."', '$stuid', '$newnotes')
	ON DUPLICATE KEY UPDATE notes = '$newnotes';";
 	mysql_query($notesql) or die (mysql_error());
	
	$commentsql = "INSERT INTO elem_stu_comments (commentid, subjectid, stuid, comments, semester, teachers, year, last_user, last_time, last_emp) VALUES ('$subjkey', '".$subjectid."', '$stuid', '$newcomments', '".$_SESSION['semester']."', '$newteachers', '$year', '$last_user', '$timestamp', '$lastemps')
	ON DUPLICATE KEY UPDATE comments = '$newcomments', teachers = '$newteachers', last_user = '$last_user', last_time ='$timestamp', last_emp = '$lastemps';";
 	mysql_query($commentsql) or die (mysql_error());
 	
 	$notesql = "INSERT INTO elem_reader_notes (noteid, subjectid, stuid, notes, semester) VALUES ('$subjkey', '".$subjectid."', '$stuid', '$new_reader_notes', '".$_SESSION['semester']."')
	ON DUPLICATE KEY UPDATE notes = '$new_reader_notes';";
 	mysql_query($notesql) or die (mysql_error());	
 	
 	// Get and update status
 	$newstatus = $_POST['status'];
	$newstatsql = "INSERT INTO elem_stu_status (statusid, subjectid, stuid, status, semester) VALUES ('$subjkey', '".$subjectid."', '$stuid', '$newstatus', '".$_SESSION['semester']."')
	ON DUPLICATE KEY UPDATE status = '$newstatus';";
 	mysql_query($newstatsql) or die (mysql_error()); 	
	
}		
$_SESSION['postname'] = null;
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Wildwood School :: Online Evaluation System</title>
    <link href="/elementary/css/main.css" media="screen" rel="stylesheet" type="text/css" />    
    <script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui-1.8.21.custom.min.js"></script>
    <script type="text/javascript" src="/js/ajax.js"></script>
    <script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
    <link rel="stylesheet" type="text/css" media="all"
          href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"    />
	<link rel="stylesheet" type="text/css" media="all"
          href="/elementary/css/tablednd.css"    />
      
	<script type="text/javascript">
	
		 function getStats(){
			var body = tinymce.get("elm1").getBody(), text = tinymce.trim(body.innerText || body.textContent);
			$("#text_num").val(text.split(/[\w\u2019\'-]+/).length);

		}
	
	
		tinyMCE.init({
			content_css : "/css/tiny_mce_custom_content.css",
			theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px",
			font_size_style_values : "10px,12px,13px,14px,16px,18px,20px",
			mode : "exact",
			gecko_spellcheck : true,

			elements : "elm1",
			theme : "advanced",
			plugins : "tinyautosave,autosave,spellchecker,paste",
			theme_advanced_buttons1 : "bold,underline,italic,bullist,numlist,spellchecker,autosave",
	//         theme_advanced_buttons2 : "pasteword",
			theme_advanced_buttons3 : "",
			spellchecker_languages : "+English=en,Spanish=es",
			valid_styles : {'*' : 'color,font-size,font-weight,font-style,text-decoration'},
			paste_use_dialog : true,
			paste_auto_cleanup_on_paste : true,
			theme_advanced_buttons1_add: "tinyautosave",
			onchange_callback: getStats
			
		});
	
		
	
	</script>
	
<script type="text/javascript">
$(document).ready(function() {

	$("#saved").fadeOut(4000);
	
	$('#reader_toggle').click(function(){
		$('#reader_panel').toggle();
		$('#reader-up').toggle();
		$('#reader-down').toggle();
	});
	
	$('#student_toggle').click(function(){
		$('#student_panel').toggle();
		$('#student-up').toggle();
		$('#student-down').toggle();
	});
	  
	$('#winter_comments').click(function(){
		$.ajax({
			type: "GET",
			url: "getComments.php?stuid=<?php echo $_GET['stuid']."&subj=".$_GET['subj']; ?>",
			success: function(msg){
				tinyMCE.get("elm1").setContent(tinyMCE.get("elm1").getContent() + msg);
			}
		});
	});

	$('#text_num').click(function(){
	
		var body = tinymce.get("elm1").getBody(), text = tinymce.trim(body.innerText || body.textContent);
		var thecount = text.split(/[\w\u2019\'-]+/).length;
		if(thecount > 300){
			$("#count").css("color","red");
		}
		else{
			$("#count").css("color","black");
		}
		$("#count").html(thecount - 1);
	});

 
});
 		
</script>
	
	<script type="text/javascript">

	function loadingAjax(){
		var stuid;	

		$.ajax({
			type: "GET",
			url: "getComments.php?stuid=<?php echo $_GET['stuid']."&subj=".$_GET['subj']; ?>",
			success: function(msg){
				tinyMCE.get("elm1").setContent(tinyMCE.get("elm1").getContent() + msg);
			}
		});
					
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
<div id="aside" style="margin-right:20px; height: 48px;"><ul ><li style="background: rgb(170,170,170);}" >

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

// Create select menu 
$selectclass = "<form name='classform' id='classform' method='POST'>Class: <select name='selectclass' onChange=\"document.classform.submit()\"><option value='$user[id]'>Please Select</option><option disabled='disabled'>&nbsp;</option>";

// Add all-grade options
foreach($gradenumbers as $key =>$gradename){
	$selectclass .= "<option value='$key'>$gradename</option>";
}

// Add break in menu
$selectclass .= "<option disabled='disabled'>&nbsp;</option>";

// Add list of elementary (campus = 1) teachers
$teachersql = "select teacher_accounts.fname,teacher_accounts.lname,teacher_accounts.id FROM teacher_accounts INNER JOIN teacher_to_class ON teacher_to_class.empID = teacher_accounts.ID WHERE teacher_accounts.campus = '1' GROUP BY teacher_accounts.fname,teacher_accounts.lname,teacher_accounts.id";
$teacherresult = mysql_query($teachersql) or die (mysql_error());
while($teacherrow = mysql_fetch_array($teacherresult)){
	$selectclass .= "<option value='".$teacherrow['id']."'>".$teacherrow['fname']."</option>";
}
$selectclass .=  "</select></form></li>";

// If a class was already selected
if(isset($_POST['selectclass'])){
	$_SESSION['selectclass'] = $_POST['selectclass'];
}
if(isset($_SESSION['selectclass'])){
	$selectclass = str_replace("<option value='".$_SESSION['selectclass']."'>","<option value='".$_SESSION['selectclass']."' selected='selected'>",$selectclass); 

	// If they selected a teacher, get the students
	if(substr($_SESSION['selectclass'],0,3) == "EMP"){
		$stusql = "SELECT student.fname,student.lname,student.id,class.name FROM student
	INNER JOIN student_to_class ON student.id = student_to_class.stuid
	INNER JOIN class
	ON student_to_class.classid = class.id
	INNER JOIN teacher_to_class
	ON class.id = teacher_to_class.classid
	WHERE teacher_to_class.empid = '".$_SESSION['selectclass']."' ORDER BY lname ASC";
	}
	
	// Otherwise they selected a grade
	else{
		$stusql = "SELECT * FROM student WHERE grade = '".$_SESSION['selectclass']."' ORDER BY lname ASC";
	}

}

// No class has been selected yet. Default to user if possible
else{

	// Get students for userid
 	$selectclass = str_replace("<option value='".$user[id]."'>","<option value='".$user[id]."' selected='selected'>",$selectclass);
	$stusql = "SELECT student.fname,student.lname,student.id,class.name FROM student
	INNER JOIN student_to_class ON student.id = student_to_class.stuid
	INNER JOIN class
	ON student_to_class.classid = class.id
	INNER JOIN teacher_to_class
	ON class.id = teacher_to_class.classid
	WHERE teacher_to_class.empid = '".$user[id]."' ORDER BY lname ASC";
	}

	// Echo class selection menu
	echo $selectclass;

	// Query for students for selected class
	$sturesult = mysql_query($stusql) or die (mysql_error());
	while($sturow = mysql_fetch_array($sturesult)){

		// Get student status badges
		$substatusimg = null;
 		$statusarray = null;
		$stustatussql = "SELECT `elem_stu_status`.`status`, `elem_stu_status`.`subjectid` FROM `elem_stu_status` WHERE stuid = '".$sturow['id']."' AND semester = '".$_SESSION['semester']."'";
		$stustatusresult = mysql_query($stustatussql) or die (mysql_error());

		// Create array of all statuses for a student
		while($statusrow = mysql_fetch_array($stustatusresult)){
			$statusarray[($statusrow['subjectid'])] .= $statusrow['status'];
		}
		$howmany = array_count_values($statusarray);

		if(($howmany['2'] == 0) && ($howmany['1'] > 0)){
			$substatusimg = "<img src='/elementary/imgs/ready-grey.png' style='float:right'>";		
		}
		if($howmany['2'] > 0) {
			$substatusimg = "<img src='/elementary/imgs/changes-grey.png' style='float:right'>";		
		}	
		if(($statusarray[$subjectid] == 1) && ($howmany['2'] == 0) && ($howmany['1'] > 1)){
			$substatusimg = "<img src='/elementary/imgs/ready-ready.png' style='float:right'>";
		}
		
		elseif(($statusarray[$subjectid] == 1) && ($howmany['2'] > 0)){
			$substatusimg = "<img src='/elementary/imgs/ready-changes.png' style='float:right'>";
		}

		elseif(($statusarray[$subjectid] == 1) && ($howmany['2'] == 0) && ($howmany['1'] == 1)){
			$substatusimg = "<img src='/elementary/imgs/ready.png' style='float:right'>";
		}
		
		elseif(($statusarray[$subjectid] == 2) && ($howmany['1'] == 0) && ($howmany['2'] > 1)){
			$substatusimg = "<img src='/elementary/imgs/changes-changes.png' style='float:right'>";
		}
		
		elseif(($statusarray[$subjectid] == 2) && ($howmany['1'] > 0)){
			$substatusimg = "<img src='/elementary/imgs/changes-ready.png' style='float:right'>";
		}

		elseif(($statusarray[$subjectid] == 2) && ($howmany['2'] == 1) && ($howmany['1'] == 0)){
			$substatusimg = "<img src='/elementary/imgs/changes.png' style='float:right'>";
		}

		elseif(($statusarray[$subjectid] == 3) && ($howmany['2'] == 0) && ($howmany['1'] > 0)){
			$substatusimg = "<img src='/elementary/imgs/complete-ready.png' style='float:right'>";
		}
		
		elseif(($statusarray[$subjectid] == 3) && ($howmany['2'] > 0)){
			$substatusimg = "<img src='/elementary/imgs/complete-changes.png' style='float:right'>";
		}

		elseif(($statusarray[$subjectid] == 3) && ($howmany['2'] == 0) && ($howmany['1'] == 0)){
			$substatusimg = "<img src='/elementary/imgs/complete.png' style='float:right'>";
		}		
	
		// Make selected student active	
		if($sturow['id'] == $_GET['stuid']){
			echo "<li id='active'>".$sturow['lname'].", ".$sturow['fname']."</li>";
			$thisstatus = $statusarray[$subjectid];
		}
		else{
			echo "<a href='?stuid=".$sturow['id']."&subj=".$subjectid."'><li>".$substatusimg.$sturow['lname'].", ".$sturow['fname']."</li></a>";
		}
	}

	echo "</ul></div><div style='margin-left:220px'>";

// If a student is selected
if(isset($_GET['stuid'])){

	// First make sure stuid is valid
	$checkstusql = "SELECT * FROM student WHERE id = '".$_GET['stuid']."'";
	$checksturesult = mysql_num_rows(mysql_query($checkstusql));
	if($checksturesult > 0){

		// Echo selected student name
		$stusql = "SELECT student.fname,student.lname,student.grade FROM student WHERE id = '".$_GET['stuid']."'";
		$sturesult = mysql_query($stusql) or die (mysql_error());
		$sturow = mysql_fetch_array($sturesult);
		$stugrade = $sturow['grade'];
		$skillgrade = $stugrade;
		$printgrade = "<b> Grade </b>".$stugrade;
		if(($stugrade == 0) || ($stugrade == 1)){
			$printgrade = "<b> Pods</b>";
			$skillgrade = 1;
		}
		$stufullname = $sturow['fname']."_".$sturow['lname'];
	
		// Button to download entire progress report
		echo "<div style='float:right; width:150px; text-align:right; margin-right:40px'><a href='http://connections.wildwood.org/elementary/download.php?stuid=".$_GET['stuid']."&sem=".$_SESSION['semester']."'>
		<img src='/images/assessments/download.png' width='37px' height='36px' style='float:right'>Download<br>Progress Report</a></div>";
		
		// Start subnav
		echo "<h3>".$sturow['fname']." ".$sturow['lname'].": ".$semesters[($_SESSION['semester'])]." ".$full_name_array[$subjectid]."</h3>";
		
		// Get subject status
		$statussql = "SELECT `elem_stu_status`.`subjectid`,`elem_stu_status`.`status` FROM elem_stu_status WHERE stuid = '".$_GET['stuid']."' AND semester = '".$_SESSION['semester']."'";
		$statusresult = mysql_query($statussql) or die (mysql_error());
		while($statusrow = mysql_fetch_array($statusresult)){
			$substatus[($statusrow['subjectid'])] = $statusrow['status'];
		}

		$subnav = "<ul id='subnav' style='margin-left:0px'>";
				
		// Get subjects in subnav
		foreach($full_abbr_array as $subloop_id => $subloop_name){
			$substatusimg = $statusbadge[($substatus[$subloop_id])];
			
			// Make selected subject current, set currentstatus value, and display subjects
			if($subloop_id == $subjectid){
				$currentstatus = $substatus[$subloop_id];

				$subnav .=  "<li class='current'>".$substatusimg."<a href='?stuid=".$_GET['stuid']."&subj=".$subloop_id."'>".$full_abbr_array[$subloop_id]."</a></li>";
			}
			else{
				$subnav .= "<li>".$substatusimg."<a href='?stuid=".$_GET['stuid']."&subj=".$subloop_id."'>".$full_abbr_array[$subloop_id]."</a></li>";
			}
		} // end subjects
		
		echo $subnav."</ul><form name='skillassessments' id='skillassessments' method='POST'>";	
	
		// Get comments and teachers
		$commentsql = "SELECT * FROM elem_stu_comments WHERE stuid = '".$_GET['stuid']."' AND subjectid = '$subjectid' AND semester = '".$_SESSION['semester']."'";
		$commentresult = mysql_query($commentsql) or die (mysql_error());
		$commentrow = mysql_fetch_array($commentresult);
		$comments = stripslashes($commentrow['comments']);
		$_SESSION['postname'] = $sturow['fname'];

		$teachers = stripslashes($commentrow['teachers']);
		if(!empty($commentrow['last_emp'])){
			$last_info = "<small><font color='#bbbbbb'>Last updated by ".$commentrow['last_user']." on ".$commentrow['last_time']."</small></font>
			<input type='hidden' name='last_emps' value='".$commentrow['last_emp']."'>";
		}
		
		// Get teacher preferences
		$prefssql = "SELECT elem_teacher_prefs.teachernames FROM elem_teacher_prefs WHERE employeeID = '".$user[id]."'";
		$prefsresult = mysql_query($prefssql) or die (mysql_error());
		$prefsrow = mysql_fetch_array($prefsresult);

		// Use name prefs if nothing is set already
		if(($teachers == "") || ($teachers == null)){
			$teachers = stripslashes($prefsrow['teachernames']);
		}		

		// Display teachers
		echo "<br><span style='font-size:110%'><b>Teachers: </b><input type='text' size='40' name='teachers' id='teachers' value='".$teachers."' style='font-size:110%'></span>
		<small><a id='rename' href='javascript:void(0);' onclick='getElementById(\"teachers\").value=\"".stripslashes($prefsrow['teachernames'])."\";getElementById(\"rename\").style.display=\"none\";getElementById(\"unname\").style.display=\"inline\"'>(use my name)</a> <a id='unname' href='javascript:void(0);' style='display:none' onclick='getElementById(\"teachers\").value=\"".$teachers."\";getElementById(\"rename\").style.display=\"inline\";getElementById(\"unname\").style.display=\"none\"'>(undo)</a>
		<br><br>";
		
		if($subjectid == 0){

		} // End Subject Zero
		
		else{

			// Get course description
			$coursesql = "SELECT * FROM elem_subject_descriptions WHERE subjectid = '$subjectid' AND grade = '$skillgrade'";
			$courseresult = mysql_query($coursesql) or die (mysql_error());
			$courserow = mysql_fetch_array($courseresult);
			$description = stripslashes($courserow['description_'.$_SESSION['semester']]);
			$webdescription = str_replace("<p>","<p style='margin:0;'>",$description);
			
			// Display course description
			echo "<table id='table' width='95%'><tr><th align='left'>&nbsp;&nbsp;Class Description</th></tr>
			<tr><td><div style='overflow: auto;color:#666;width:100%;height:150px;font-family:\"lucida sans\",sans-serif;font-size:12px;' >".$webdescription."</div></td></tr></table><br>";
		
			// Create array of all skills on page
			$postdata = array();
	
			// Show headings for selected subject
			$headingsql = "SELECT * FROM elem_headings WHERE subjectid = '$subjectid' OR subjectid = '0' ORDER BY heading_sort ASC";
			$headingresult = mysql_query($headingsql) or die (mysql_error());
			while($headingrow = mysql_fetch_array($headingresult)){
				$skillsql = "SELECT * FROM elem_skills WHERE headingid = '".$headingrow['headingid']."' AND subjectid = '$subjectid' AND grade = '$skillgrade' AND (semester = '".$_SESSION['semester']."' OR semester = '0') ORDER BY skill_sort ASC";
				$skillresult = mysql_query($skillsql) or die (mysql_error());
		
				// Only echo heading if it has skills associated for given grade
				if((mysql_num_rows($skillresult)) > 0){
		
					// Echo heading
					if($_SESSION['semester'] == 2){
						$sem1 = "<th width='30px' align='center'><font color='777777'><small>&nbsp;&nbsp;Winter</small></font></th>";
					}
					echo "<table id='table' width='95%'>$sem1<th colspan='2' align='left'>&nbsp;&nbsp;".$headingrow['heading']."</th></tr>
					";			
			
					// Clear an existng skill table and create new for PDF
					$pdf_skill_table = "";
					$pdf_skill_rows = "";
					$pdf_skill_table = "<table width='100%'><tr><td colspan='5' class='noBorder'><b>".$headingrow['heading']."</b></td></tr>";
		
					// For each skill, first get assessment set
					while($skillrow = mysql_fetch_array($skillresult)){
						$set = $skillrow['set_id'];
						$skillid = $skillrow['skillid'];
			
						// Add the skill to the post array
						$postdata[] = $skillid;
						
						// Clear PDF rows
						$pdf_toprow_abbrs = "";
						$pdf_assess_row = "";
						$pdf_row_color = "";
							
						// Echo assessment selection

						echo "<tr>";
						if($_SESSION['semester'] == 2){
							$stuskillsql1 = "SELECT * FROM elem_stu_skills WHERE skillid = '".$skillid."' AND stuid = '".$_GET['stuid']."' AND semester = '1'";
							$stuskillresult1 = mysql_query($stuskillsql1) or die (mysql_error());				
							$stuskillrow1 = mysql_fetch_assoc($stuskillresult1);
							$stuassessment1 = $stuskillrow1['assessid'];
							echo "<td align='center'><font color='777777'>".$assess_abbr_array[$stuassessment1]."</font></td>";
						}
						echo "<td width='20%'><select name='skill_".$skillid."'>";
	
						$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '$set' OR set_id = '0' ORDER BY assess_sort DESC";
						$assessresult = mysql_query($assesssql) or die (mysql_error());
						while($assessrow = mysql_fetch_array($assessresult)){
					
							// Create top row for PDF
							$pdf_toprow_abbrs .= "<td width='30'><center>".$assessrow['assess_abbr']."</center></td>";  
							
								// Set selected value in assessment select menu
		
								// First check if skill has been assessed before
								$stuskillsql = "SELECT * FROM elem_stu_skills WHERE skillid = '".$skillid."' AND stuid = '".$_GET['stuid']."' AND semester = '".$_SESSION['semester']."'";
								$stuskillresult = mysql_query($stuskillsql) or die (mysql_error());				
								$stuskillrow = mysql_fetch_assoc($stuskillresult);
								$stuassessment = $stuskillrow['assessid'];
	
								if((mysql_num_rows($stuskillresult) > 0) && ($stuskillrow['assessid'] != "spr") ){								
									if($stuassessment == $assessrow['assessid']){
										echo "<option value = '".$assessrow['assessid']."' selected='selected'>".$assessrow['assess_name']."</option>";
										$pdf_assess_row .= "<td><center>X</center></td>";
									}
									else{
										echo "<option value = '".$assessrow['assessid']."'>".$assessrow['assess_name']."</option>";
										$pdf_assess_row .= "<td></td>";
									}
								}
								
								// If skill hasn't been assessed, set to default
								else{
										if($assessrow['deft'] == 1){
											echo "<option value = '".$assessrow['assessid']."' selected='selected'>".$assessrow['assess_name']."</option>";
										}
										else{
											echo "<option value = '".$assessrow['assessid']."'>".$assessrow['assess_name']."</option>";								
										}
								// Add blank boxes to PDF for unassessed skills
								$pdf_assess_row .= "<td></td>";	
								}
							}
						
						$skill = stripslashes($skillrow['skill']);
						echo "</select></td><td>".$skill."</td></tr>
						";
						$pdf_skill_rows .= "<tr ".$pdf_row_color.">".$pdf_assess_row."<td>".(rtrim($skill, '.'))."</td></tr>
						";
					}
					
					// Turn skill numbers array into CSV for POST
					$skillspost = implode(",", $postdata);
					echo "</table><br><input type='hidden' name='skillspost' value='".$skillspost."'>";
				}	
			}
		} // end normal subjects
			
		// Get notes
		$notesql = "SELECT elem_stu_notes.notes FROM elem_stu_notes WHERE stuid = '".$_GET['stuid']."' AND subjectid = '$subjectid'";
		$noteresult = mysql_query($notesql) or die (mysql_error());
		$noterow = mysql_fetch_array($noteresult);
		$notes = stripslashes($noterow['notes']);
		
		// Get reader notes
		$rnotesql = "SELECT elem_reader_notes.notes FROM elem_reader_notes WHERE stuid = '".$_GET['stuid']."' AND subjectid = '$subjectid' AND semester = '".$_SESSION['semester']."'";
		$rnoteresult = mysql_query($rnotesql) or die (mysql_error());
		$rnoterow = mysql_fetch_array($rnoteresult);
		$reader_notes = stripslashes($rnoterow['notes']);
	
		// Display comments and notes fields
		echo "<table id='table' width='95%' ><tr><th align='left'>&nbsp;&nbsp;Progress Report Comments";
		echo "</th></tr></table><textarea id='elm1' name='comments' rows='14' style='color:#222;width:95%;font-family:\"lucida sans\",sans-serif;'>".htmlentities($comments)."</textarea></table>";
		echo "<table id='table' width='95%' ><tr valign='middle'>
		<th align='left'><div id='reader_toggle' style='float:left;width:30%;margin:5px;'>".$statusicon[($currentstatus)]."<div style='display:inline;vertical-align:top'>&nbsp;<img id='reader-up' src='/images/assessments/arrow-up.png'><img id='reader-down' src='/images/assessments/arrow-down.png' style='display:none'>&nbsp;Reader Notes</div></div>";
		if($_SESSION['semester'] == 2){
				echo "<div id='winter_comments' style='float:left;width:30%;margin:5px;'><img src='/elementary/imgs/add.png'><div style='display:inline;vertical-align:top'>&nbsp;Add Winter Comments</div></div>";
		}
		echo "<div id='text_num' style='float:right;width:25%;margin:5px;'><img src='/elementary/imgs/count.png'><div style='display:inline;vertical-align:top'>&nbsp;Word Count: &nbsp;<div style='display:inline' id='count'></div></div></div>";
		echo "</div></th></tr><tbody id='reader_panel' style='display:none'><tr><td><textarea name='reader_notes' rows='10' style='font-size:14px;border:none;color:#222;width:100%;font-family:\"lucida sans\",sans-serif;'>".$reader_notes."</textarea></td></tr>
		</tbody></table>
		<br><table id='table' width='95%' ><tr id='student_toggle'><th align='left'>&nbsp;<img id='student-up' src='/images/assessments/arrow-up.png'><img id='student-down' src='/images/assessments/arrow-down.png' style='display:none'>&nbsp;Student Notes (only visible internally)</th></tr><tbody id='student_panel' style='display:none'><tr><td><textarea name='notes' rows='10' style='font-size:14px;border:none;color:#222;width:100%;font-family:\"lucida sans\",sans-serif;'>".$notes."</textarea></td></tr>
		</tbody></table>
		<br><br><h3>Status: ";
		
		// Set status menu		
		$statselect = "<select name='status' style='font-size:95%'>";
		foreach($statusvalues as $key => $statusname){
			if($key == $currentstatus){
				$statselect .= "<option value='".$key."' selected='selected'>".$statusname."</option>";
			}
			else{
				$statselect .= "<option value='".$key."'>".$statusname."</option>";
			}	
		}
		$statselect .= "</select>";
		echo $statselect;
		
		// Status email
		$prevstatus = $_POST['prevstatus'];
		if(($prevstatus != 2) && ($newstatus == 2)){
// 		if(($newstatus == 2)){
			$email_authors = explode(",",$commentrow['last_emp']);
			foreach($email_authors as $author){
				$usernamesql = "SELECT teacher_accounts.username FROM teacher_accounts WHERE id = '$author'";
				$usernameresult = mysql_query($usernamesql) or die (mysql_error());
				$usernamerow = mysql_fetch_array($usernameresult);
				if($usernamerow['username'] != null){
					$authoremail[] = $usernamerow['username']."@wildwood.org";
				}
				
			}
			$mailto = implode(",",$authoremail);
			$mailsubject = "Progress report for ".$sturow['fname']." ".$sturow['lname']." needs changes.";
			$mailmessage = "<html><head><style>
				body {margin:26px; color: #222222; font: 14px Verdana, Arial, Helvetica, sans-serif; line-height: 100%; }
				a {color:#636363;}
				a:visited {color:#636363;}
				a:active {color:#444444;}
				a { outline: 0; }
				</style></head><body>
				Hello,<br><br> 
				".$user['fname']." ".$user['lname']." has marked the progress report for ".$sturow['fname']." ".$sturow['lname']." as needing teacher changes.
				<br><br>
				Please review the notes below and click <a href='http://connections.wildwood.org".$pageurl."&sem=".$_SESSION['semester']."'>here</a> to make the necessary changes.
				<br><br>Reader Notes:<br>".$reader_notes."</body></html>";
				
			$mailfrom = $user['fname']." ".$user['lname']." <".$user['name']."@wildwood.org>";
			$mailheaders = "MIME-Version: 1.0" . "\r\n";
			$mailheaders .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
			$mailheaders .= "From:".$mailfrom. "\r\n";
			mail(($mailto),$mailsubject,$mailmessage,$mailheaders);
		}
		
		
		echo "<input type='hidden' name='prevstatus' value='$currentstatus'>";
		if((($_SESSION['real_year'] == $_SESSION['year']) || (strpos($user[flags], "a")) !== false)){
			echo "<input name='submit' type='submit' style='margin-right:30px;float:right;font-size:100%' value='Save Section'>";
		}
		else{
			echo "<input disabled='disabled' type='submit' style='margin-right:30px;float:right;font-size:100%' value='Saving Disabled'>";
		}
		echo "</h3>".$last_info."</form>";
	}		
}
?>
</div>
<div style="padding-bottom:50px"></div>
<?php

?>
 
 
 
 
 
    