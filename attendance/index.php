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
<div id="aside" style="margin-right:20px; height: 48px;"><ul >

<?php
// Create year selection menu
$selectyear = "<li style='background: rgb(170,170,170);}' ><form name='yearform' id='yearform' method='POST'>Year: <select name='year' onChange=\"document.yearform.submit()\">";
foreach($yeararray as $years){
	$selectyear .= "<option value='$years'>".($years-1)."-$years</option>";
}
$selectyear .="</select></form><br>";
$selectyear = str_replace("<option value='".$_SESSION['year']."'>","<option value='".$_SESSION['year']."' selected='selected'>",$selectyear);

// Create semester selection menu -- DISABLED --
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
	INNER JOIN class ON student_to_class.classid = class.id
	INNER JOIN teacher_to_class ON class.id = teacher_to_class.classid
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
if( (strpos($user[flags], "a")) !== false){
	echo $selectyear;
	echo $selectclass;
}
	// Query for students for selected class
	$sturesult = mysql_query($stusql) or die (mysql_error());
	while($sturow = mysql_fetch_array($sturesult)){
	
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
// 		echo "<div style='float:right; width:150px; text-align:right; margin-right:40px'><a href='http://connections.wildwood.org/elementary/download.php?stuid=".$_GET['stuid']."&sem=".$_SESSION['semester']."'>
// 		<img src='/images/assessments/download.png' width='37px' height='36px' style='float:right'>Download<br>Progress Report</a></div>";
		
		// Start subnav
		echo "<h3>".$sturow['fname']." ".$sturow['lname']." Attendance</h3>";
		
	
	}		
}
?>
</div>
<div style="padding-bottom:50px"></div>
<?php

?>
 
 
 
 
 
    