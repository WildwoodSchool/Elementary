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


// Count number of subjects (used to check if all are complete) if not already done
if(!isset($_SESSION['subjcount'])){
	$countresult = mysql_query("SELECT count(1) FROM elem_subjects");
	$countrow = mysql_fetch_array($countresult);
	$_SESSION['subjcount'] = $countrow[0];
}

$bulktest = array(
	1 => "STU000793",
	2 => "STU000782",
	3 => "STU004813",
	4 => "STU000771",
	5 => "STU001916",
);

foreach($bulktest as $stuid){
$pdf_complete = "";
$pdf_header = "";
$pdf_content = "";
echo $stuid."<br>";

//$semester = $_GET['sem'];
$semester = 1;


// Get student name
$stusql = "SELECT student.fname,student.lname,student.grade FROM student WHERE id = '".$stuid."'";
$sturesult = mysql_query($stusql) or die (mysql_error());
$sturow = mysql_fetch_array($sturesult);
$stugrade = $sturow['grade'];
$printgrade = "<b> Grade </b>".$stugrade;
if(($stugrade == 0) || ($stugrade == 1)){
	$printgrade = "<b> Pods</b>";
}
$stufullname = $sturow['fname']."_".$sturow['lname'];


// $subjsql = "SELECT elem_subjects.subject_name,subject_abbr FROM elem_subjects WHERE subjectid = '$subjectid'";
// $subjresult = mysql_query($subjsql) or die (mysql_error());
// $subjrow = mysql_fetch_array($subjresult);
// $subjectname = str_replace(" ","_",$subjrow['subject_name']);
$dldate = date("m.d.y");
$filename = $_SERVER['DOCUMENT_ROOT'] . "/assessmentsFile/rubrics/". $stuid ."_s".$semester."_full.pdf";
$filearray[] = $filename;

$subcount = 0;

$subjsql = "SELECT * FROM elem_subjects ORDER BY subject_name ASC";
$subjresult = mysql_query($subjsql) or die (mysql_error());

// Start Subject Loop -------------------------------------------------------------------------------------------------------------------
while($subjrow = mysql_fetch_array($subjresult)){

	$subjectid = $subjrow['subjectid'];
	$subcount = $subcount + 1;

	// Create PDF header
	if($subcount == 1){
		$pdf_content .= "<div id='header' ><div id='text'><strong><font size='+1'>Wildwood Elementary School<br />
		".$semesters[($semester)]." Progress Report: ".$subjrow['subject_name']."<br />
		" .$sturow['fname']." ".$sturow['lname']. " - " . $printgrade ."</font></strong></div></div>";
	}
	else{
		$pdf_content .= "</span><div style='page-break-before: always;'></div><div id='header' ><div id='text'><strong><font size='+1'>Wildwood Elementary School<br />
		".$semesters[($semester)]." Progress Report: ".$subjrow['subject_name']."<br />
		" .$sturow['fname']." ".$sturow['lname']. " - " . $printgrade ."</font></strong></div></div>";
	}	
	// Get comments and teachers
	$commentsql = "SELECT elem_stu_comments.comments,elem_stu_comments.teachers FROM elem_stu_comments WHERE stuid = '".$stuid."' AND subjectid = '$subjectid' AND semester = '".$semester."'";
	$commentresult = mysql_query($commentsql) or die (mysql_error());
	$commentrow = mysql_fetch_array($commentresult);
	$comments = stripslashes($commentrow['comments']);
	$teachers = stripslashes($commentrow['teachers']);
	$pdf_content .= "<span style='font-size: 12px'><b>Teacher(s): </b> " . $teachers. "<br /><br />";

	// Get course description
	$coursesql = "SELECT * FROM elem_subject_descriptions WHERE subjectid = '$subjectid' AND grade = '$stugrade'";
	$courseresult = mysql_query($coursesql) or die (mysql_error());
	$courserow = mysql_fetch_array($courseresult);
	$description = stripslashes($courserow['description_'.$semester]);
			
	// Add course description to PDF
	$pdf_content .= "<b>Description:</b><br />".$description."<span style='color: #fff'>-</span><br />";
	
	$headingsql = "SELECT * FROM elem_headings WHERE subjectid = '$subjectid' OR subjectid = '0' ORDER BY heading_sort ASC";
	$headingresult = mysql_query($headingsql) or die (mysql_error());

	// Start Headings Loop -----------------------------------------------------------------------------------------------------------------
	while($headingrow = mysql_fetch_array($headingresult)){
		$skillsql = "SELECT * FROM elem_skills WHERE headingid = '".$headingrow['headingid']."' AND grade = '$stugrade' AND semester <= '".$semester."' ORDER BY skill_sort ASC";
		$skillresult = mysql_query($skillsql) or die (mysql_error());

		// Only echo heading if it has skills associated for given grade
		if((mysql_num_rows($skillresult)) > 0){
				
			// Clear an existng skill table and create new for PDF
			$pdf_skill_table = "";
			$pdf_skill_rows = "";
			$pdf_skill_table = "<table width='100%'><tr><td colspan='5' class='noBorder'><b>".$headingrow['heading']."</b></td></tr>";

			// Start Skill Loop ----------------------------------------------------------------------------------------------------------------
			while($skillrow = mysql_fetch_array($skillresult)){
				$set = $skillrow['set_id'];
				$skillid = $skillrow['skillid'];
						
				// Clear PDF rows
				$pdf_toprow_abbrs = "";
				$pdf_assess_row = "";
				$pdf_row_color = "";
					
				// For skills only to be displayed (not assessed)
				if(($semester ==  1) && ($skillrow['semester'] == 1)){
					$pdf_assess_row .= "<td></td><td></td><td></td><td></td>";
					$pdf_row_color = "bgcolor='#BDECEB'";

					// Still need to create top row for PDF
					$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '$set' ORDER BY assess_sort DESC";
					$assessresult = mysql_query($assesssql) or die (mysql_error());
					while($assessrow = mysql_fetch_array($assessresult)){
						$pdf_toprow_abbrs .= "<td width='30'><center>".$assessrow['assess_abbr']."</center></td>";  	
					}
				}
			
				// For assessed skills, get assessment
				else{
					$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '$set' ORDER BY assess_sort DESC";
					$assessresult = mysql_query($assesssql) or die (mysql_error());

					// Loop Assessments Values -----------------------------------------------------------------------------------------------------
					while($assessrow = mysql_fetch_array($assessresult)){
				
						// Create top row for PDF
						$pdf_toprow_abbrs .= "<td width='30'><center>".$assessrow['assess_abbr']."</center></td>";  
						
						// Get skill assessment
						$stuskillsql = "SELECT * FROM elem_stu_skills WHERE skillid = '".$skillid."' AND stuid = '".$stuid."' AND semester = '".$semester."'";
						$stuskillresult = mysql_query($stuskillsql) or die (mysql_error());				
						$stuskillrow = mysql_fetch_assoc($stuskillresult);
						$stuassessment = $stuskillrow['assessid'];

						// Get winter assessments
						if((mysql_num_rows($stuskillresult) > 0) && ($stuskillrow['assessid'] != "spr") ){								
							if($stuassessment == $assessrow['assessid']){
								$pdf_assess_row .= "<td><center>X</center></td>";
							}
							else{
								$pdf_assess_row .= "<td></td>";
							}
						}
						
						// Add blank boxes to PDF for spring skills
						else{
							$pdf_assess_row .= "<td></td>";	
						}
						
					} // End Assessment Values loop
					
				} // End get assessment
				
				// Put together skill row
				$skill = stripslashes($skillrow['skill']);
				$pdf_skill_rows .= "<tr ".$pdf_row_color.">".$pdf_assess_row."<td>".(rtrim($skill, '.'))."</td></tr>
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
	$pdf_content .= "<b>Comments</b><br />
	".$comments."<span style='color: #fff;'>-</span><br /><span style='color: #000;'>&nbsp;";

} // End Subject Loop
	
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

table{
	border-collapse: collapse;
	page-break-inside: avoid;
	font-size:12px;
}

td{
	padding-left:1px;
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
  $pdf->page_text(120, 750, "12201 Washington Place, Los Angeles, CA 90066  Phone: (310) 397-3134", $font, 9, array(0,0,0));
  
  $pdf->page_text(290, 760, " - {PAGE_NUM} - ", $font, 9, array(0,0,0));

}
</script>';

$pdf_complete = $pdf_header . $pdf_content ."</body></html>";
echo $stufullname."<br>";


ob_start();
	include('../dompdf/dompdf_config.inc.php');
ob_end_clean();

$html = $pdf_complete;

$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();

// header('Content-type: application/pdf');
// header('Content-Disposition: attachment; filename="'.$_GET['output'].'.pdf"');
$output = $dompdf->output();

file_put_contents($filename, $output);

}






// header('Content-Type: application/download');
// header('Content-Disposition: attachment; filename=' . $filename);
// header("Content-Length: " . filesize($filename));
// 
// $fp = fopen($filename, "r");
// fpassthru($fp);
// fclose($fp);

//unlink($filename);
?>