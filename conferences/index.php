<?php

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info] 
require ("../includes/usercheck.php");

// Connect to elementary DB
require ("../includes/database.php");

// Get Array Values
require ("../includes/arrays.php");


// // Get semester selection, guess if not selected
// if((!isset($_SESSION['semester'])) && (!isset($_POST['semester'])) ){
// 	if(((date(n)) >= 7) || ((date(n)) == 1)){
// 		$_SESSION['semester'] = 1;
// 	}
// 	else{
// 		$_SESSION['semester'] = 2;
// 	}
// }
// elseif(isset($_POST['semester'])){
// 	$_SESSION['semester'] = $_POST['semester'];
// }


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
	
	// Get and update notes and comments
	$newcomments = mysql_real_escape_string($_POST['comments']);
	$newcomments = str_replace("#fname",$_SESSION['postname'],$newcomments);

	$newteachers = mysql_real_escape_string($_POST['teachers']);
	$newattendees = mysql_real_escape_string($_POST['attendees']);
	
	$last_user = $user[fname]." ".$user[lname];
	$subjkey = $stuid."_s1_conf";
// 	$subjkey = $stuid."_s".$_SESSION['semester']."_conf";

	$month = date(n);

	require ("../includes/datecheck.php");


//Figure out what year to print
// 	if(($month >= 7) && ($_SESSION['semester']  == 3)){
// 		$year = date(Y) + 1;
// 	}
// 	if(($month < 7) && ($_SESSION['semester']  == 3)){
// 		$year = date(Y);
// 	}
// 	if($_SESSION['semester']  == 2){
// 		$year = date(Y);
// 	}
	
	$commentsql = "INSERT INTO elem_stu_conferences (commentid, stuid, comments, semester, teachers, year, last_user, last_time, emps, attendees) VALUES ('$subjkey', '$stuid', '$newcomments', '1', '$newteachers', '$year', '$last_user', '$timestamp', '$user[id]', '$newattendees')
	ON DUPLICATE KEY UPDATE comments = '$newcomments', teachers = '$newteachers', last_user = '$last_user', last_time ='$timestamp', emps = '$user[id]', attendees = '$newattendees';";
 	mysql_query($commentsql) or die (mysql_error());
 	

 	

	
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

// // Create semester selection menu
// $selectsemester = "<form name='semesterform' id='semesterform' method='POST'>Semester: <select name='semester' onChange=\"document.semesterform.submit()\">";
// foreach($semesters as $key =>$semestername){
// 	$selectsemester .= "<option value='$key'>$semestername</option>";
// }
// $selectsemester .="</select></form><br>";
// $selectsemester = str_replace("<option value='".$_SESSION['semester']."'>","<option value='".$_SESSION['semester']."' selected='selected'>",$selectsemester);
// echo $selectsemester;

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
	
		// Make selected student active	
		if($sturow['id'] == $_GET['stuid']){
			echo "<li id='active'>".$sturow['lname'].", ".$sturow['fname']."</li>";
			$thisstatus = $statusarray[$subjectid];
		}
		else{
			echo "<a href='?stuid=".$sturow['id']."'><li>".$substatusimg.$sturow['lname'].", ".$sturow['fname']."</li></a>";
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
		echo "<div style='float:right; width:150px; text-align:right; margin-right:40px'><a href='http://connections.wildwood.org/elementary/confsummary.php?stuid=".$_GET['stuid']."&sem=1'>
		<img src='/images/assessments/download.png' width='37px' height='36px' style='float:right'>Download<br>Summary</a></div>";
		
		// Start subnav (semester options)
// 		echo "<h3>".$sturow['fname']." ".$sturow['lname'].": ".$semesters[($_SESSION['semester'])]." Conference Summary</h3>";

		// Start subnav Fall ONLY
		echo "<h3>".$sturow['fname']." ".$sturow['lname'].": Fall Conference Summary</h3>";

		
		echo "<form name='conferencenotes' id='notes' method='POST'>";	
	
		// Get comments and teachers
		$commentsql = "SELECT * FROM elem_stu_conferences WHERE stuid = '".$_GET['stuid']."' AND semester = '1'";
		$commentresult = mysql_query($commentsql) or die (mysql_error());
		$commentrow = mysql_fetch_array($commentresult);
		$comments = stripslashes($commentrow['comments']);

		$_SESSION['postname'] = $sturow['fname'];

		$teachers = stripslashes($commentrow['teachers']);
		$attendees = stripslashes($commentrow['attendees']);
		
		if(!empty($commentrow['last_user'])){
			$last_info = "<small><font color='#bbbbbb'>Last updated by ".$commentrow['last_user']." on ".$commentrow['last_time']."</small></font>";
		}
		
		// Get teacher preferences
		$prefssql = "SELECT elem_teacher_prefs.teachernames FROM elem_teacher_prefs WHERE employeeID = '".$user[id]."'";
		$prefsresult = mysql_query($prefssql) or die (mysql_error());
		$prefsrow = mysql_fetch_array($prefsresult);

		// Use name prefs if nothing is set already
		if(($teachers == "") || ($teachers == null)){
			$teachers = stripslashes($prefsrow['teachernames']);
		}		

		if(($attendees == "") || ($attendees == null)){
			$attendees = stripslashes($prefsrow['teachernames']);
		}	
		
		
		// Display teachers
		echo "<br><span style='font-size:110%'><b>Teachers: </b><input type='text' size='40' name='teachers' id='teachers' value='".$teachers."' style='font-size:110%'></span>
		<small><a id='rename' href='javascript:void(0);' onclick='getElementById(\"teachers\").value=\"".stripslashes($prefsrow['teachernames'])."\";getElementById(\"rename\").style.display=\"none\";getElementById(\"unname\").style.display=\"inline\"'>(use my name)</a> <a id='unname' href='javascript:void(0);' style='display:none' onclick='getElementById(\"teachers\").value=\"".$teachers."\";getElementById(\"rename\").style.display=\"inline\";getElementById(\"unname\").style.display=\"none\"'>(undo)</a>
		<br><br></small>";

		// Display Attending
		echo "<br><span style='font-size:110%'><b>In Attendance: </b><input type='text' size='40' name='attendees' id='attendees' value='".$attendees."' style='font-size:110%'></span>
		<br><br>";				
			
		// Get notes
		$notesql = "SELECT * FROM elem_stu_notes WHERE stuid = '".$_GET['stuid']."'";
		$noteresult = mysql_query($notesql) or die (mysql_error());
		while($noterow = mysql_fetch_array($noteresult)){
			$subj = $noterow['subjectid'];
			$notes .= $full_name_array[$subj].":\n".stripslashes($noterow['notes'])."\n\n"	;
		}
				
// 		Get reader notes
// 		$rnotesql = "SELECT elem_reader_notes.notes FROM elem_reader_notes WHERE stuid = '".$_GET['stuid']."' AND subjectid = '$subjectid' AND semester = '".$_SESSION['semester']."'";
// 		$rnoteresult = mysql_query($rnotesql) or die (mysql_error());
// 		$rnoterow = mysql_fetch_array($rnoteresult);
// 		$reader_notes = stripslashes($rnoterow['notes']);
	
		// Display comments and notes fields
		echo "<table id='table' width='95%' ><tr><th align='left'>&nbsp;&nbsp;Conference Summary";
		echo "</th></tr></table><textarea id='elm1' name='comments' rows='34' style='color:#222;width:95%;font-family:\"lucida sans\",sans-serif;'>".htmlentities($comments)."</textarea></table>";
		echo "<table id='table' width='95%' ><tr valign='middle'>
		<th align='left'>";
		echo "<div id='text_num' style='float:right;width:25%;margin:5px;'><img src='/elementary/imgs/count.png'><div style='display:inline;vertical-align:top'>&nbsp;Word Count: &nbsp;<div style='display:inline' id='count'></div></div></div>";
		echo "</div></th></tr><tbody id='reader_panel' style='display:none'><tr><td><textarea name='reader_notes' rows='10' style='font-size:14px;border:none;color:#222;width:100%;font-family:\"lucida sans\",sans-serif;'>".$reader_notes."</textarea></td></tr>
		</tbody></table>
		<br><table id='table' width='95%' ><tr id='student_toggle'><th align='left'>&nbsp;<img id='student-up' src='/images/assessments/arrow-up.png'><img id='student-down' src='/images/assessments/arrow-down.png' style='display:none'>&nbsp;Combined Student Notes</th></tr><tbody id='student_panel' style='display:none'><tr><td><textarea name='notes' rows='10' disabled = 'disabled' style='font-size:14px;border:none;color:#222;width:100%;font-family:\"lucida sans\",sans-serif;'>".$notes."</textarea></td></tr>
		</tbody></table>
		<br><br>";
		
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
 
 
 
 
 
    