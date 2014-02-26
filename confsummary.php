<?php
session_start();

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info] 
require ("includes/usercheck.php");

// Connect to elementary DB
require ("includes/database.php");

// Get Array Values
require ("includes/arrays.php");

if(isset($_GET['stuid'])){
	$_SESSION['dlarray'] = array(0 => $_GET['stuid'],);
	$_SESSION['dlsemester'] = $_GET['sem'];
}

if( (isset($_SESSION['dlarray'])) && (!empty($_SESSION['dlarray'])) ){

	
	/* creates a compressed zip file */
	function create_zip($files = array(),$destination = '',$overwrite = true) {
	  //if the zip file already exists and overwrite is false, return false
	  if(file_exists($destination) && !$overwrite) { return false; }
	  //vars
	  $valid_files = array();
	  //if files were passed in...
	  if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
		  //make sure the file exists
		  if(file_exists($file)) {
			$valid_files[] = $file;
		  }
		}
	  }
	  //if we have good files...
	  if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
		  return false;
		}
		//add the files
		foreach($valid_files as $file) {
		  $zip->addFile($file,$file);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	  }
	  else
	  {
		return false;
	  }
	}
	
	$semester = $_SESSION['dlsemester'];
		
	$zipname = "PDFs/Conference_Summaries.zip";
	
	foreach($_SESSION['dlarray'] as $stuid){	

// DEBUG // DEBUG // DEBUG // DEBUG // DEBUG // DEBUG // DEBUG // DEBUG // DEBUG // DEBUG 
// echo $stuid;

		$pdf_header = "";
		$pdf_content = "";
		$teacherarray = "";
// 		$pdf_assess_row = "";
// 		$pdf_toprow_abbrs = "";
// 		$pdf_assess_row = "";
// 		$pdf_skill_rows = "";
// 		$pdf_skill_table = "";
// 		$pdf_content = "";
// 		$key_toprow_abbrs = "";
// 		$key_col = "";
// 		$key_rows = "";
// 		$springcols = "";
		$pdf_key = "";
	
		// PDF File Creation

// Get student name
$stusql = "SELECT student.fname,student.lname,student.grade FROM student WHERE id = '".$stuid."'";
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

// Get head teacher for student
$headteachersql = "SELECT `elem_teacher_prefs`.`teachernames`
	FROM `elem_teacher_prefs`
	INNER JOIN `teacher_to_class` ON `elem_teacher_prefs`.`employeeID` = `teacher_to_class`.`empid`
	INNER JOIN `student_to_class` ON `teacher_to_class`.`classid` = `student_to_class`.`classid`
	WHERE `student_to_class`.`stuid` = '$stuid'";
$headteacherresult =  mysql_query($headteachersql) or die (mysql_error());
$headteacherrow = mysql_fetch_array($headteacherresult);
$headteacher = $headteacherrow['teachernames'];
$teacherarray = array(0 => $headteacher,);

$dldate = date("m.d.y");
$filename = "PDFs/". $sturow['lname']."_".$sturow['fname']."_Conference_Summary.pdf";
$phpname = "PDFs/". $sturow['lname']."_".$sturow['fname']."_Conference_Summary.php";

$filearray[] .= $filename;

$subcount = 0;

// $subjsql = "SELECT * FROM elem_subjects ORDER BY subject_sort ASC";
// $subjresult = mysql_query($subjsql) or die (mysql_error());

	// Get comments and teachers
	$commentsql = "SELECT * FROM elem_stu_conferences WHERE stuid = '".$stuid."' AND semester = '".$semester."'";
	$commentresult = mysql_query($commentsql) or die (mysql_error());
	$commentrow = mysql_fetch_array($commentresult);
	$comments = stripslashes($commentrow['comments']);
	$teachers = stripslashes($commentrow['teachers']);
	$attendees = stripslashes($commentrow['attendees']);

	if(empty($schoolyear)){
		$year = $commentrow['year'];
		$schoolyear = ($year - 1)."-".$year;
	}	
	
// 		Create PDF header with different semesters
// 		$pdf_content .= "<div id='header' ><div id='text'><strong><font size='+1'>Wildwood Elementary School<br />
// 		".$semesters[($semester)]." Conference Summary<br />
// 		" .$sturow['fname']." ".$sturow['lname']. " - " . $printgrade ."</font></strong></div></div>";

		// Create PDF header for Fall ONLY
		$pdf_content .= "<div id='header' ><div id='text'><strong><font size='+1'>Wildwood Elementary School<br />
		Fall $schoolyear Conference Summary<br />
		" .$sturow['fname']." ".$sturow['lname']. " - " . $printgrade ."</font></strong></div></div>";

				
		if((strpos($teachers, ", ")) || (strpos($teachers, " & ")) || (strpos($teachers, " and "))){
			$pdf_content .= "<span style='font-size: 12px'><br /><br /><b>Teachers: </b> " . $teachers. "<br /><br />";
		}
		else{
			$pdf_content .= "<span style='font-size: 12px'><br /><br /><b>Teacher: </b> " . $teachers. "<br /><br />";
		}
				
		// Add attendees description to PDFs
		$pdf_content .= "<b>In Attendance:</b> ".$attendees."<span style='color: #fff'>-</span><br /><br />";
							
		// Add comments to PDF
		$pdf_content .= "<div style='page-break-before: auto;'><b>Comments: </b><br />
		".$comments."</div><span style='color: #fff;'>-</span><br /><span style='color: #000;'>&nbsp;";
		
	
// PDF File Creation
$pdf_header = "<html><head>
<style type='text/css'>
html,body {
	font-family:interstate;
	height:100%;
	width:100%;
	overflow:auto;
	margin-left: 40px;
	margin-right: 40px;
	margin-top: 30px;	
	margin-bottom:40px;
	font-size:12px;
}

p {
	margin:0px;
}

table{
	border-collapse: collapse;
	page-break-inside: avoid;
	font-size:12px;
}

td{
	padding-left:2px;
	border: 1px solid #000
}
.noBorder {
	border: 0;
}

#header {
	background:#ffffff url('http://connections.wildwood.org/assessmentsFile/rubrics/gradient.png') no-repeat center center;
	height: 100px;
}


#text {
	position:relative;
	text-align:center;
	padding:10px;
}

</style></head><body>";

$pdf_header .= '<script type="text/php">

if (isset($pdf) ) {

  $font = Font_Metrics::get_font("interstate-light");
  $pdf->page_text(150, 750, "12201 Washington Place, Los Angeles, CA 90066  Phone: (310) 397-3134", $font, 9, array(0,0,0));
  
  $pdf->page_text(290, 760, " - {PAGE_NUM} - ", $font, 9, array(0,0,0));

}
</script>';

$pdf_complete = $pdf_header . $pdf_key . $pdf_content ."</span></body></html>";

		// DEBUG - Write PHP files	
// 		echo  $pdf_key . $pdf_content ."</body></html>";
// 
//         if (!$handle = fopen($phpname, 'w+')) {
//           echo "Cannot open file ($phpname)";
//           exit;
//         }
//         // Write $somecontent to our opened file.
//         if (fwrite($handle, $pdf_complete) === false) {
//           echo "Cannot write to file ($phpname)";
//           exit;
//         }


	if(count($_SESSION['dlarray']) > 1){		
		ob_start();
			include("../dompdf/dompdf_config.inc.php");
		ob_end_clean();
	
		$dompdf = null;
		$dompdf = new DOMPDF();
		$dompdf->load_html($pdf_complete);
		$dompdf->render();
		$output = $dompdf->output();
		
		file_put_contents($filename, $output);
		} // End large array check 

	
		} // end pdf creation loop
		
	if(count($_SESSION['dlarray']) == 1){		
		ob_start();
			include("../dompdf/dompdf_config.inc.php");
		ob_end_clean();
		
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($pdf_complete);
		$dompdf->render();
		
		header('Content-type: application/pdf');
		header('Content-Disposition: attachment; filename="'.$sturow['fname'].'_'.$sturow['lname'].'-'.$semesters[$semester].'_Progress_Report-'.$dldate.'.pdf');
		
		echo $dompdf->output();
	}



	if(count($_SESSION['dlarray']) > 1){		
		
		create_zip($filearray,$zipname);
		$result = create_zip($filearray,$zipname);
		
		
		//header ("Location: http://connections.wildwood.org/elementary/".$zipname);
		echo "<center><a href='http://connections.wildwood.org/elementary/".$zipname."'><img src='/elementary/imgs/zipdl.png' height='40px'><br>
		Your progress reports are ready. Click here to download.</a></center>";
		
		foreach($filearray as $deleteme){
			unlink($deleteme);
		}
		
	} // End large array check 
}

?>