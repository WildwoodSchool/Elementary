<?php


// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info] 
require ("../includes/usercheck.php");

// Connect to elementary DB
require ("../includes/database.php");

// Get Array Values
require ("../includes/arrays.php");


$stuid = $_GET['stuid'];
$semester = $_GET['sem'];
$stugrade = $_GET['grade'];
$skillgrade = $stugrade;
$printgrade = "<b> Grade </b>".$stugrade;
if(($stugrade == 0) || ($stugrade == 1)){
	$printgrade = "<b> Pods</b>";
	$skillgrade = 1;
}
	
$subcount = 0;

$subjsql = "SELECT * FROM elem_subjects ORDER BY subject_sort ASC";
$subjresult = mysql_query($subjsql) or die (mysql_error());

// Start Subject Loop -------------------------------------------------------------------------------------------------------------------
while($subjrow = mysql_fetch_array($subjresult)){

	$subjectid = $subjrow['subjectid'];
	$subcount = $subcount + 1;

	// Create PDF header
	$pdf_content .= "</span><div style='height:300px;'></div><div id='header' ><div id='text'><strong><font size='+1'>Wildwood Elementary School<br />
	".$semesters[($semester)]." Progress Report: ".$subjrow['subject_name']."<br />
	Template Preview - " . $printgrade ."</font></strong></div></div>";
		

	// Get comments and teachers
	$pdf_content .= "<span style='font-size: 12px'><b>Teacher(s): </b><br /><br />";

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
				$set = $skillrow['set_id'];
				$skillid = $skillrow['skillid'];
						
				// Clear PDF rows
				$pdf_toprow_abbrs = "";
				$pdf_assess_row = "";
				$pdf_row_color = "";
				$pdf_row_img = "";
					

				$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '$set' ORDER BY assess_sort DESC";
				$assessresult = mysql_query($assesssql) or die (mysql_error());

				// Loop Assessments Values ------------------------------------------------------------------------------------------------------
				while($assessrow = mysql_fetch_array($assessresult)){
			
					// Create top row for PDF
					$pdf_toprow_abbrs .= "<td width='30'><center>".$assessrow['assess_abbr']."</center></td>";  
										
					// Add blank boxes to PDF for spring skills
					
						$pdf_assess_row .= "<td ></td>";	
					
					
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
	$pdf_content .= "<b>Comments: </b><br />
	".$comments."<span style='color: #fff;'>-</span><br /><span style='color: #000;'>&nbsp;";

} // End Subject Loop




// PDF Key
$pdf_key = "<div id='header' ><div id='text'><strong><font size='+1'>Wildwood Elementary School<br />
		".$semesters[($semester)]." ".date('Y')." Progress Report<br />
		Template Preview - " . $printgrade ."</font></strong></div></div>
		<span style='font-size: 12px'><br /><br /><b>Teachers: </b><br /><br /><b>Evaluation Rubric:</b><br />
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
	$assess_descrip = str_replace("#fname", "the student", $assessrow['assess_descrip']);
	$key_rows .= "<tr valign='middle'>".$key_col."<td><b>". $assessrow['assess_name']." (".$assessrow['assess_abbr']."): </b> ".$assess_descrip."</td></tr>
	";
	$springcols .= "<td height='25'></td>";
}

		$unassesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '0'";
		$unassessresult = mysql_query($unassesssql) or die (mysql_error());
		$unassessrow = mysql_fetch_array($unassessresult);
		$unassess_descrip = str_replace("#fname", "the student", $unassessrow['assess_descrip']);
	
		$springrow = "<tr bgcolor='".$prefarray['spring_color']."'>".$springcols."
			<td valign='middle'>".$unassess_descrip."</td>
		</tr>";

//$springrow = "<tr bgcolor='#a1dadc'>".$springcols."<td valign='middle'>Shaded skills will be assessed in the Spring Semester.</td></tr>";
		
$pdf_key .= "<table width='100%'>
	<tr>".$key_toprow_abbrs."<td valign='middle'><b>Explanation:</b></td></tr>
	".$key_rows;
	
	$pdf_key .= $springrow;

$pdf_key .=	"</table><br>
	<br><b>".$prefarray['social_notes_name'].":</b><br><br></span>
		<span style='color: #fff;'>-</span><br /><span style='color: #000;'>&nbsp;
		";

	
// PDF File Creation
$pdf_header = "<html><head>
<style type='text/css'>
html,body {
	font-family:interstate-light,interstate,'Interstate', 'Interstate-Light', sans-serif;
	width:600px;
	padding:40px;
	font-size:12px;
}

p {
	margin:0px;
}

table{
	font-family:interstate-light,interstate,'Interstate', 'Interstate-Light', sans-serif;
	border-collapse: collapse;
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
	font-family:interstate-light,'Interstate', 'Interstate-Light', sans-serif;
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

$pdf_complete = $pdf_header . $pdf_key . $pdf_content ."</body></html>";

//   echo  $pdf_key . $pdf_content ."</body></html>";

echo $pdf_complete;


// Direct Link, but security risk
// $output =  $dompdf->output();
// file_put_contents($filename2, $output);
// $pdf_link = "http://connections.wildwood.org/assessmentsFile/rubrics/". $stuid ."_s".$semester."_full.pdf";
// header ("Location: ".$pdf_link);

?>