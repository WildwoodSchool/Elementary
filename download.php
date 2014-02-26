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

$year = $_SESSION['year'];

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
		
	$zipname = "PDFs/".$semesters[$semester]."_".date(Y)."_Progress_Reports.zip";
	
foreach($_SESSION['dlarray'] as $stuid){	
	
	// DEBUG // DEBUG // DEBUG // DEBUG // DEBUG // DEBUG // DEBUG // DEBUG // DEBUG // DEBUG 
	// echo $stuid;
	
	$pdf_header = "";
	$pdf_content = "";
	$teacherarray = "";
	$pdf_assess_row = "";
	$pdf_toprow_abbrs = "";
	$pdf_assess_row = "";
	$pdf_skill_rows = "";
	$pdf_skill_table = "";
	$pdf_content = "";
	$key_toprow_abbrs = "";
	$key_col = "";
	$key_rows = "";
	$springcols = "";
	$pdf_key = "";
	$abs = "";
	$tar = "";

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
	
	// Class Notes (subject zero)
	$classsql = "SELECT elem_stu_comments.comments,elem_stu_comments.teachers FROM elem_stu_comments WHERE stuid = '".$stuid."' AND subjectid = '0' AND semester = '".$semester."'";
	$classresult = mysql_query($classsql) or die (mysql_error());
	$classrow = mysql_fetch_array($classresult);
	$classnotes = stripslashes($classrow['comments']);
	
	
	$dldate = date("m.d.y");

	if($_SESSION['collate'] == 'true'){
		$pdfpath = $headteacher."/";
		if (!file_exists()) {
			mkdir('PDFs/'.$pdfpath, 0777, true);
			$teacherfolderarray[] = "PDFs/".$pdfpath;
		}
	}

	$filename = "PDFs/".$pdfpath. $sturow['lname']."_".$sturow['fname']."_".$semesters[$semester]."_".date(Y)."_Progress_Report.pdf";
	$phpname = "PDFs/".$pdfpath. $sturow['lname']."_".$sturow['fname']."_".$semesters[$semester]."_".date(Y)."_Progress_Report.php";
	$filearray[] .= $filename;
	
	$subcount = 0;
	
	// $subjsql = "SELECT * FROM elem_subjects ORDER BY subject_sort ASC";
	// $subjresult = mysql_query($subjsql) or die (mysql_error());
	
	// Start Subject Loop -------------------------------------------------------------------------------------------------------------------
	// while($subjrow = mysql_fetch_array($subjresult)){
	foreach($subj_name_array as $subjectid => $subj_name){
	
	//	$subjectid = $subjrow['subjectid'];
		$subcount = $subcount + 1;
	
		// Get comments and teachers
		$commentsql = "SELECT elem_stu_comments.comments,elem_stu_comments.year,elem_stu_comments.teachers FROM elem_stu_comments WHERE stuid = '".$stuid."' AND subjectid = '$subjectid' AND semester = '".$semester."'";
		$commentresult = mysql_query($commentsql) or die (mysql_error());
		$commentrow = mysql_fetch_array($commentresult);
		$comments = stripslashes($commentrow['comments']);
		$teachers = stripslashes($commentrow['teachers']);
		if(empty($schoolyear)){
			$year = $commentrow['year'];
			$schoolyear = ($year - 1)."-".$year;
		}
		
		// Make sure comment field is filled out, otherwise do not generate report for subject
		if(!empty($commentrow['comments'])){
			// Create PDF header
			$pdf_content .= "</span><div style='page-break-before: always;'></div><div id='header' ><div id='text'><strong><font size='+1'>Wildwood Elementary School<br />
			".$semesters[($semester)]." $schoolyear Progress Report: ".$subj_name."<br />
			" .$sturow['fname']." ".$sturow['lname']. " - " . $printgrade ."</font></strong></div></div>";
				
		
		
			$teacherarray[] .= $teachers;
			if((strpos($teachers, ", ")) || (strpos($teachers, " & ")) || (strpos($teachers, " and "))){
				$pdf_content .= "<span style='font-size: 12px'><b>Teachers: </b> " . $teachers. "<br /><br />";
			}
			else{
				$pdf_content .= "<span style='font-size: 12px'><b>Teacher: </b> " . $teachers. "<br /><br />";
			}
			// Get course description
			$coursesql = "SELECT * FROM elem_subject_descriptions WHERE subjectid = '$subjectid' AND grade = '$skillgrade'";
			$courseresult = mysql_query($coursesql) or die (mysql_error());
			$courserow = mysql_fetch_array($courseresult);
			$description = stripslashes($courserow['description_'.$semester]);
					
			// Add course description to PDF
			$pdf_content .= "<b>Description:</b><br />".$description."<span style='color: #fff'>-</span><br />";
			
			$headingsql = "SELECT * FROM elem_headings WHERE subjectid = '$subjectid' OR subjectid = '0' ORDER BY heading_sort ASC";
			$headingresult = mysql_query($headingsql) or die (mysql_error());
		
			// Start Headings Loop --------------------------------------------------------------------------------------------------------------------------
			while($headingrow = mysql_fetch_array($headingresult)){
				$skillsql = "SELECT * FROM elem_skills WHERE headingid = '".$headingrow['headingid']."' AND subjectid = '$subjectid' AND grade = '$skillgrade' AND (semester = '".$semester."' OR semester = '0') ORDER BY skill_sort ASC";
				$skillresult = mysql_query($skillsql) or die (mysql_error());
		
				// Only echo heading if it has skills associated for given grade
				if((mysql_num_rows($skillresult)) > 0){
						
					// Clear an existng skill table and create new for PDF
					$pdf_skill_table = "";
					$pdf_skill_rows = "";
					$pdf_skill_table = "<table width='100%'><tr><td colspan='5' class='noBorder'><b>".$headingrow['heading']."</b></td></tr>";
		
					// Start Skill Loop ---------------------------------------------------------------------------------------------------------------------
					while($skillrow = mysql_fetch_array($skillresult)){
						if($set != $skillrow['set_id']){
							$set = $skillrow['set_id'];
							$assessarray = null;
							$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '$set' ORDER BY assess_sort DESC";
							$assessresult = mysql_query($assesssql) or die (mysql_error());
							while($assessrow = mysql_fetch_array($assessresult)){
								$assessarray[($assessrow['assessid'])] .= $assessrow['assess_abbr'];
							}
						}
	
	// 					$set = $skillrow['set_id'];
						$skillid = $skillrow['skillid'];
								
						// Clear PDF rows
						$pdf_toprow_abbrs = "";
						$pdf_assess_row = "";
						$pdf_row_color = "";
						$pdf_row_img = "";
			
						// Loop Assessments Values ------------------------------------------------------------------------------------------------------
						foreach($assessarray as $assessid => $assessabbr){
							// Create top row for PDF
							$pdf_toprow_abbrs .= "<td width='30'><center>".$assessabbr."</center></td>";  
							
							// Get skill assessment
							$stuskillsql = "SELECT * FROM elem_stu_skills WHERE skillid = '".$skillid."' AND stuid = '".$stuid."' AND semester = '".$semester."'";
							$stuskillresult = mysql_query($stuskillsql) or die (mysql_error());				
							$stuskillrow = mysql_fetch_assoc($stuskillresult);
							$stuassessment = $stuskillrow['assessid'];
		
							// Get winter assessments
							if(mysql_num_rows($stuskillresult) > 0){								
								if($stuassessment == $assessid){
									$pdf_assess_row .= "<td><center>X</center></td>";
								}
								elseif($stuassessment == "10"){
									$pdf_row_color = "bgcolor='".$prefarray['spring_color']."'";							
									$pdf_assess_row .= "<td ></td>";
								}
								else{
									$pdf_assess_row .= "<td ></td>";
								}
							}
							
							// Add blank boxes to PDF for spring skills
							else{
								$pdf_assess_row .= "<td ></td>";	
							}
							
						} // End Assessment Values loop
							
						
						// Put together skill row
						$skill = stripslashes($skillrow['skill']);
						$pdf_skill_rows .= "<tr ".$pdf_row_color.">".$pdf_assess_row."<td >".(rtrim($skill, '.'))."</td></tr>
						";
					} // End Skill Loop
					
					// Put together heading table
					$pdf_skill_table .= "<tr>".$pdf_toprow_abbrs."<td>Skills and Content:</td></tr>
					".$pdf_skill_rows."</table><span style='color: #fff'>-</span><br />
					";
					$pdf_content .= $pdf_skill_table;
					
					}  // End heading-with-skills action
		
				}  // End Heading Loop
					
			// Add comments to PDF
			$pdf_content .= "<div style='page-break-before: auto;'><b>Comments: </b><br />
			".$comments."</div><span style='color: #fff;'>-</span><br /><span style='color: #000;'>&nbsp;";
			}
		else{
		}
	
	} // End Subject Loop
	
	$allteachers = implode(", ",array_unique($teacherarray));
	$allteachers = str_replace(", and ", ", ", $allteachers);
	$allteachers = str_replace(" and ", ", ", $allteachers);
	$allteachers = str_replace(" & ", ", ", $allteachers);
	$allteachers = explode(", ", $allteachers);
	$allteachers = implode(", ",array_unique($allteachers));
	
	// PDF Key
	$pdf_key = "<div id='header' ><div id='text'><strong><font size='+1'>Wildwood Elementary School<br />
			".$semesters[($semester)]." ".$schoolyear." Progress Report<br />
			" .$sturow['fname']." ".$sturow['lname']. " - " . $printgrade ."</font></strong></div></div>";
			
	// Rubric Table		
	$pdf_key .=	"<br /><br /><b>Evaluation Rubric Key:</b><br />
			";
	$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '1' ORDER BY assess_sort DESC";
	$assessresult = mysql_query($assesssql) or die (mysql_error());
	$assesscount = mysql_num_rows($assessresult);
	
	while($assessrow = mysql_fetch_array($assessresult)){
		$rownumber = $assessrow['assess_sort'];
		$key_toprow_abbrs .= "<td width='30' height='20' valign='middle' align='center'><b>".$assessrow['assess_abbr']."</b></td>";  	
		$key_col = "";
		for ($i=1; $i<=$assesscount; $i++){
			$backcount = abs($i - ($assesscount + 1));	
			if( $backcount == $rownumber){
				$key_col .= "<td  height='25'><center>X</center></td>";
			}
			else{
				$key_col .= "<td ></td>" ;
			}
		
		}
		$assess_descrip = str_replace('#fname',$sturow['fname'],$assessrow['assess_descrip']);
		$key_rows .= "<tr valign='middle'>".$key_col."<td><b>". $assessrow['assess_name']." (".$assessrow['assess_abbr']."): </b> ".$assess_descrip."</td></tr>
		";
		$springcols .= "<td height='25'></td>";
	}
	
			$unassesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '0'";
			$unassessresult = mysql_query($unassesssql) or die (mysql_error());
			$unassessrow = mysql_fetch_array($unassessresult);
			$unassess_descrip = str_replace('#fname',$sturow['fname'],$unassessrow['assess_descrip']);
	
			$springrow = "<tr bgcolor='".$prefarray['spring_color']."'>".$springcols."
				<td valign='middle'>".$unassess_descrip."</td>
			</tr>";
	
	//$springrow = "<tr bgcolor='#a1dadc'>".$springcols."<td valign='middle'>Shaded skills will be assessed in the Spring Semester.</td></tr>";
			
	$pdf_key .= "<table width='100%'>
		<tr>".$key_toprow_abbrs."<td valign='middle'><b>Explanation:</b></td></tr>
		".$key_rows;
		
		$pdf_key .= $springrow;
	
	$pdf_key .=	"</table>
	
	<span style='font-size: 12px'><br /><br /><b>Teachers: </b>".$allteachers."<br />";
	
	
	// Attendance
	if($semester == 1){
		$range = "AND `att_date` > '".($year - 1)."-07-01' AND `att_date` < '".$year."-01-01'";
	}
	elseif($semester == 2){
		$range = "AND `att_date` > '".$year."-01-01' AND `att_date` < '".$year."-07-01'";
	}
	
	$attendancesql = "SELECT * FROM elem_stu_attendance WHERE stuid = '$stuid' $range";
	$attendanceresult = mysql_query($attendancesql) or die (mysql_error());
	
	$abs[] = array();
	$tar[] = array();
	
	while($attendancerow = mysql_fetch_array($attendanceresult)){
		if(substr($attendancerow['status'],0,3) == "Abs"){
			$abs[] = $attendancerow['date'];
		}
		elseif(substr($attendancerow['status'],0,3) == "Tar"){
			$tar[] = $attendancerow['date'];
		}
	}

	$pdf_key .= "<br><b>Absences: </b>".(count($abs) - 1).";&nbsp;&nbsp;&nbsp;&nbsp;<b>Tardies: </b>".(count($tar) - 1)."<br />";
	// Social/Emotional Class Notes
	if(!empty($classnotes)){
		$pdf_key .= "<br><b>".$prefarray['social_notes_name'].":</b><br>".$classnotes."<br>";
	}
	
	$pdf_key .= "</span>
			<span style='color: #fff;'>-</span><br /><span style='color: #000;'>&nbsp;
			";
	
		
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
		
// 		foreach($teacherfolderarray as $deleteme){
// 			echo $deleteme;
// 			unlink($deleteme);
// 		} 
		
		
	} // End large array check 
}

?>