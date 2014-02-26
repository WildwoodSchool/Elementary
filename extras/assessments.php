<?php

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info] 
require "includes/usercheck.php";

// Connect to elementary DB
require "includes/database.php";

// Get Array Values
include "includes/arrays.php";


// Failed attempt at using Zend auth. QQ
// 	$auth = Zend_Auth::getInstance();
// 	$username = str_replace("WILDWOOD\\", "",$auth->getIdentity());
//  echo $username;

// Test account
// $user[name] = "zshaffer";
// $user[id] = "EMP000254";



// Count number of subjects (used to check if all are complete) if not already done
if(!isset($_SESSION['subjcount'])){
	$countresult = mysql_query("SELECT count(1) FROM elem_subjects");
	$countrow = mysql_fetch_array($countresult);
	$_SESSION['subjcount'] = $countrow[0];
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
	$stuid = $_GET['stuid'];
	$skillspost = $_POST['skillspost'];

	// Turn skillspost back into array
	$skillspost = explode(",",$skillspost);

	// Loop through array of skills getting each value and updating/inserting into table
	foreach($skillspost as $num){
		$skillnumb = "skill_".$num;
		$stuskillval = $_POST[$skillnumb];
		$skillkey = $stuid."_skill".$num;
		$thesql = "INSERT INTO elem_stu_skills (stu_assessment_key, assessid, stuid, skillid) VALUES ('$skillkey', '$stuskillval', '$stuid', '$num')
		ON DUPLICATE KEY UPDATE assessid = '$stuskillval';";
 		mysql_query($thesql) or die (mysql_error());
	}
	// Get and update notes and comments
	$newcomments = mysql_real_escape_string($_POST['comments']);
	$newnotes = mysql_real_escape_string($_POST['notes']);
	$subjkey = $stuid."_subj".$subjectid;

	$notesql = "INSERT INTO elem_stu_notes (noteid, subjectid, stuid, notes) VALUES ('$subjkey', '".$subjectid."', '$stuid', '$newnotes')
	ON DUPLICATE KEY UPDATE notes = '$newnotes';";
 	mysql_query($notesql) or die (mysql_error());
	
	$commentsql = "INSERT INTO elem_stu_comments (commentid, subjectid, stuid, comments) VALUES ('$subjkey', '".$subjectid."', '$stuid', '$newcomments')
	ON DUPLICATE KEY UPDATE comments = '$newcomments';";
 	mysql_query($commentsql) or die (mysql_error());
 	
 	// Get and update status
 	$newstatus = $_POST['status'];
	$newstatsql = "INSERT INTO elem_stu_status (statusid, subjectid, stuid, status) VALUES ('$subjkey', '".$subjectid."', '$stuid', '$newstatus')
	ON DUPLICATE KEY UPDATE status = '$newstatus';";
 	mysql_query($newstatsql) or die (mysql_error()); 	
	
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
<body>

<div id="wrapper">
    <div id="header">

        <div id="userPanel">


        </div>
        <div id="searchFloat">

        <input type="text" size="20" onkeyup="showResult(this.value)" id="searchInput" style="width:120px;" />


                <div id="livesearch"></div></div>
                <img src="/images/assessmentsLogo.png" style="padding:10px 20px 30px 20px;">
<?php

// Echo Navbar
include "includes/navbar.php";
?>
    <div id="content">
    <div id="aside" style="margin-right:20px; height: 48px;"><form name="classform" id="classform" method="POST"><ul><li style="background: rgb(170,170,170);}" >Class:
<?php   

// Create select menu 
$selectclass = "<select name='selectclass' onChange=\"document.classform.submit()\"><option value='$user[id]'>Select a Class</option><option disabled='disabled'>&nbsp;</option>";

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
		$stustatussql = "SELECT elem_stu_status.status FROM elem_stu_status WHERE stuid = '".$sturow['id']."'";
		$stustatusresult = mysql_query($stustatussql) or die (mysql_error());

		// Create array of all statuses for a student
		while($statusrow = mysql_fetch_array($stustatusresult)){
			$statusarray[] = $statusrow['status'];
		}

		// Badge for Ready for Review status
		if(in_array(1, $statusarray)){
			$substatusimg = "<img src='/elementary/imgs/ready.png' style='float:right'>";
		}
		
		// Overwrite if there is are Needs Changes statuses
		if(in_array(2, $statusarray)){
			$substatusimg = "<img src='/elementary/imgs/changes.png' style='float:right'>";
		}

		// If all subjects are marked Complete
		if((array_sum($statusarray)) == ($_SESSION['subjcount'] * 3)){
			$substatusimg = "<img src='/elementary/imgs/complete.png' style='float:right'>";
		}	
		
		// Make selected student active	
		if($sturow['id'] == $_GET['stuid']){
			echo "<li id='active'>".$sturow['fname']." ".$sturow['lname']."</li>";
		}
		else{
			echo "<a href=?stuid=".$sturow['id']."><li>".$substatusimg.$sturow['fname']." ".$sturow['lname']."</li></a>";
		}
	}

	echo "</ul></div>";

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
		echo "<h3>".$sturow['fname']." ".$sturow['lname']."</h3><ul id='subnav'>";
	
	// Show subnavbar

		// List subjects in subnav
		$subjsql = "SELECT * FROM elem_subjects ORDER BY subject_name ASC";
		$subjresult = mysql_query($subjsql) or die (mysql_error());
		while($subjrow = mysql_fetch_array($subjresult)){
		
			// Get subject status
			$statussql = "SELECT elem_stu_status.status FROM elem_stu_status WHERE stuid = '".$_GET['stuid']."' AND subjectid = '".$subjrow['subjectid']."'";
			$statusresult = mysql_query($statussql) or die (mysql_error());
			$statusrow = mysql_fetch_array($statusresult);
			$substatus = $statusrow['status'];
			$substatusimg = $statusbadge[$substatus];
			
			// Make current subject current and set current status
			if($subjrow['subjectid'] == $subjectid){
				$currentstatus = $substatus;
				echo "<li class='current'>".$substatusimg."<a href='?stuid=".$_GET['stuid']."&subj=".$subjrow['subjectid']."'>".$subjrow['subject_abbr']."</a></li>";
			}
			else{
				echo "<li>".$substatusimg."<a href='?stuid=".$_GET['stuid']."&subj=".$subjrow['subjectid']."'>".$subjrow['subject_abbr']."</a></li>";
			}
		}
		echo "</ul><div style='margin-left:230px'><form name='skillassessments' method='POST'>";	
	
		// Create array of all skills on page
		$postdata = array();
		
		// Show headings for selected subject
		$headingsql = "SELECT * FROM elem_headings WHERE subjectid = '$subjectid' OR subjectid = '0' ORDER BY heading_sort ASC";
		$headingresult = mysql_query($headingsql) or die (mysql_error());
		while($headingrow = mysql_fetch_array($headingresult)){
			$skillsql = "SELECT * FROM elem_skills WHERE headingid = '".$headingrow['headingid']."' AND grade = '$stugrade' ORDER BY skill_sort ASC";
			$skillresult = mysql_query($skillsql) or die (mysql_error());
	
			// Only echo heading if it has skills associated for given grade
			if((mysql_num_rows($skillresult)) > 0){
	
				// Echo heading
				echo "<table id='table' width='95%'><th colspan='2' align='left'>&nbsp;&nbsp;".$headingrow['heading']."</th>";			
	
				// For each skill, first get assessment set
				while($skillrow = mysql_fetch_array($skillresult)){
					$set = $skillrow['set_id'];
					$skillid = $skillrow['skillid'];
		
					// Add the skill to the array
					$postdata[] = $skillid;
					
					// Echo assessment selection
					echo "<tr><td width='20%'><select name='skill_".$skillid."'>";
					$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '$set' ORDER BY assess_sort DESC";
					$assessresult = mysql_query($assesssql) or die (mysql_error());
					while($assessrow = mysql_fetch_array($assessresult)){
		
					// Set selected value in assessment select menu
		
						// First check if skill has been assessed before
						$stuskillsql = "SELECT * FROM elem_stu_skills WHERE skillid = '".$skillid."' AND stuid = '".$_GET['stuid']."'";
						$stuskillresult = mysql_query($stuskillsql) or die (mysql_error());				
						if(mysql_num_rows($stuskillresult) > 0){
							$stuskillrow = mysql_fetch_assoc($stuskillresult);
							$stuassessment = $stuskillrow['assessid'];
							if($stuassessment == $assessrow['assessid']){
								echo "<option value = '".$assessrow['assessid']."' selected='selected'>".$assessrow['assess_name']."</option>";
							}
							else{
								echo "<option value = '".$assessrow['assessid']."'>".$assessrow['assess_name']."</option>";
							}
						}
						
						// If skill hasn't been assessed, then go to default
						elseif(mysql_num_rows($stuskillresult) == 0){
							if($assessrow['deft'] == 1){
								echo "<option value = '".$assessrow['assessid']."' selected='selected'>".$assessrow['assess_name']."</option>";
							}
							else{
								echo "<option value = '".$assessrow['assessid']."'>".$assessrow['assess_name']."</option>";
							}
						}
					}
					echo "</select></td><td>".$skillrow['skill']."</td></tr>";
				}
				
				// Turn skill numbers array into CSV for POST
				$skillspost = implode(",", $postdata);
				echo "</table><br><input type='hidden' name='skillspost' value='".$skillspost."'>";
			}	
		}
		
		// Get comments
		$commentsql = "SELECT elem_stu_comments.comments FROM elem_stu_comments WHERE stuid = '".$_GET['stuid']."' AND subjectid = '$subjectid'";
		$commentresult = mysql_query($commentsql) or die (mysql_error());
		$commentrow = mysql_fetch_array($commentresult);
		$comments = stripslashes($commentrow['comments']);
	
		// Get notes
		$notesql = "SELECT elem_stu_notes.notes FROM elem_stu_notes WHERE stuid = '".$_GET['stuid']."' AND subjectid = '$subjectid'";
		$noteresult = mysql_query($notesql) or die (mysql_error());
		$noterow = mysql_fetch_array($noteresult);
		$notes = stripslashes($noterow['notes']);
	
		// Display comments and notes fields	
		echo "<table id='table' width='95%' ><tr><th align='left'>&nbsp;&nbsp;Public Comments (will be printed on student assessment)</th></tr></table><textarea name='comments' rows='10' style='color:#444;width:95%;font-family:\"lucida sans\",sans-serif;'>".$comments."</textarea>
		<br><br><table id='table' width='95%' ><tr><th align='left'>&nbsp;&nbsp;Internal Notes (only visible to Wildwood teachers)</th></tr></table><textarea name='notes' rows='10' style='color:#444;width:95%;font-family:\"lucida sans\",sans-serif;'>".$notes."</textarea>
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
		echo "<input name='submit' type='submit' style='margin-right:30px;float:right;font-size:100%' value='Save Section'></h3></form>";
	}		
}
?>
</div>
<div style="padding-bottom:50px"></div>

    