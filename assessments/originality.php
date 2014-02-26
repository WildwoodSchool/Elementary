<?php

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info] 
require ("../includes/usercheck.php");

// Connect to elementary DB
require ("../includes/database.php");

// Get Array Values
require ("../includes/arrays.php");


// Get semester selection, guess if not selected
if((!isset($_SESSION['semester'])) && (!isset($_POST['semester'])) ){
	if(((date(n)) >= 7) || ((date(n)) == 1)){
		$_SESSION['semester'] = 1;
	}
	else{
		$_SESSION['semester'] = 2;
	}
}
elseif(isset($_POST['semester'])){
	$_SESSION['semester'] = $_POST['semester'];
}


// Default to general subject if one isn't selected
if(!isset($_GET['subj'])){
	$subjectid = 0;
}

// Otherwise use the selected subject	
else{
	$subjectid = $_GET['subj'];
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

// Echo Navbar
include ("../includes/navbar.php");

?>
	
<div id="content">
<div id="aside" style="margin-right:20px; height: 48px;"><ul ><li style="background: rgb(170,170,170);}" >

<?php

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
	WHERE teacher_to_class.empid = '".$_SESSION['selectclass']."' ORDER BY fname ASC";
	}
	
	// Otherwise they selected a grade
	else{
		$stusql = "SELECT * FROM student WHERE grade = '".$_SESSION['selectclass']."' ORDER BY fname ASC";
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
	WHERE teacher_to_class.empid = '".$user[id]."' ORDER BY fname ASC";
	}

	// Echo class selection menu
	echo $selectclass;

	// Query for students for selected class
	$sturesult = mysql_query($stusql) or die (mysql_error());
	while($sturow = mysql_fetch_array($sturesult)){

		// Get student status badges
		$substatusimg = null;
 		$statusarray = null;
		$stustatussql = "SELECT elem_stu_status.status, elem_stu_status.subjectid FROM elem_stu_status WHERE stuid = '".$sturow['id']."' AND semester = '".$_SESSION['semester']."'";
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
			echo "<li id='active'>".$sturow['fname']." ".$sturow['lname']."</li>";
			$thisstatus = $statusarray[$subjectid];
		}
		else{
			echo "<a href='?stuid=".$sturow['id']."&subj=".$subjectid."'><li>".$substatusimg.$sturow['fname']." ".$sturow['lname']."</li></a>";
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
// Display teachers
		echo "<br><span style='font-size:110%'><b>Teachers: </b><input type='text' size='40' name='teachers' id='teachers' value='".$teachers."' style='font-size:110%'></span>
		<small><a id='rename' href='javascript:void(0);' onclick='getElementById(\"teachers\").value=\"".stripslashes($prefsrow['teachernames'])."\";getElementById(\"rename\").style.display=\"none\";getElementById(\"unname\").style.display=\"inline\"'>(use my name)</a> <a id='unname' href='javascript:void(0);' style='display:none' onclick='getElementById(\"teachers\").value=\"".$teachers."\";getElementById(\"rename\").style.display=\"inline\";getElementById(\"unname\").style.display=\"none\"'>(undo)</a>
		<br><br>";
	
		// Display comments and notes fields
		echo "<table id='table' width='95%' ><tr><th align='left'>&nbsp;&nbsp;Progress Report Comments";
		echo "</th></tr></table><textarea id='elm1' name='comments' rows='14' style='color:#222;width:95%;font-family:\"lucida sans\",sans-serif;'>".htmlentities($comments)."</textarea></table></small>";
		// If submitted, get POST data

		$stuid = $_GET['stuid'];
		// Get and update notes and comments
		$origsql = "SELECT `elem_stu_comments`.`comments`,`elem_stu_comments`.`stuid` FROM `elem_stu_comments` WHERE `elem_stu_comments`.`teachers` = '$teachers' AND `elem_stu_comments`.`subjectid` = '$subjectid'";
		$origresult = mysql_query($origsql) or die (mysql_error());
		echo "<br><br>Similar Comments:<br>";
		while($origrow = mysql_fetch_array($origresult)){
			similar_text( $origrow['comments'], $comments, $percent); 
			$percent = round($percent);
			if(($percent != 0) && ($percent < 98)){
				$origarray[] = $percent;
				if($percent > 60){
					$unorigarray[] = $percent;
				}
				if($percent > 60){
					echo "<a href='?stuid=".$origrow['stuid']."&subj=".$subjectid."'>".$origrow['stuid'].": ".$percent."%<br></a>";
				}
			 }

		}	
		$max = max($origarray);
		$total_count = count($origarray);
		$unorig_count = count($unorigarray);
		$multiplier = (($total_count - $unorig_count)/$total_count);
// 		$multiplier = 1;
		$algo = ((((array_sum($origarray)/count($origarray)) + (array_sum($unorigarray)/count($unorigarray)))/2) + $max)/3 ;
		$originality = round((100 - $algo) * $multiplier);
		$originality = round((($originality - 15)/85) * 100);
		$green = round(2.55 * $originality) + 10;
		$red = round(255 - (2.55 * abs($originality - $algo)));
	// 	echo var_dump($origarray)."<br><br>";
// 		echo var_dump($unorigarray);
		echo "<br><br>Number of assessments compared: ".count($origarray)."<br>Average Match: ".(array_sum($origarray)/count($origarray))."<br>$multiplier<br>Total: $total_count / $unorig_count<br>Closet Match: ".$max."%<br><br>Originality Score: 
		<div style='font-size:20px;width:75px;height:20px;padding:5px;background-color:rgb(".$red.",".$green.",0);'>".$originality."/100</div>";
	}				
}
?>
</div>
<div style="padding-bottom:50px"></div>
<?php

?>
 
 
 
 
 
    