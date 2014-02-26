<?php
$filename = 'pdfs/' . $stuid . '_subj' . $subjectid . '.php';
$somecontent = "
<html>
<head>
<style type='text/css'>
html,body {
	 font-family:interstate;
	height:100%;
	width:100%;
	overflow:auto;
	margin-left: 40px;
	margin-right: 40px;
	margin-top: 20px;	
 margin-bottom:40px;
}



table
{
  border-collapse: collapse;
  page-break-inside: avoid;
  font-size:15px;

}

td
{
border: 1px solid #000

}
.noBorder {
    border: 0
}


#header {background:#ffffff url('gradient.png') no-repeat center center;
height: 100px;

}

#text {
position:relative;
text-align:center;
padding:10px;
}

</style>
</head>
<body>";

$somecontent .= '<script type="text/php">

if (isset($pdf) ) {

  $font = Font_Metrics::get_font("interstate-light");
  $pdf->page_text(120, 750, "Wildwood School, 11811 W. Olympic Blvd., Los Angeles, CA 90064 Phone: (310) 478-7189", $font, 9, array(0,0,0));
  
  $pdf->page_text(290, 760, " - {PAGE_NUM} - ", $font, 9, array(0,0,0));

}
</script>';

$sql2 = "select class,name from rubrics where id = '".$_GET['rubric']."'";
$result2 = mysql_query($sql2) or die (mysql_error());
$row2=mysql_fetch_assoc($result2);
$class = $row2['class'];
$rubricname = $row2['name'];

$sql = "select * from class where name = '$class'";
$result = mysql_query($sql) or die (mysql_error());
$row=mysql_fetch_assoc($result);

if($gradeLevel == "upper") {
$somecontent .= "<div id='header'><div id='text'><strong><font size='+1'>Wildwood Upper School<br />";
}
elseif($gradeLevel == "middle") {
$somecontent .= "<div id='header'><div id='text'><strong><font size='+1'>Wildwood Middle School<br />";
}

$classname = $row['description'];


$sql3 = "select * from honors where studentid = '".$_GET['student']."' AND classid = '".$row['name']."'";
$result3 = mysql_query($sql3) or die (mysql_error());
$row3=mysql_fetch_assoc($result3);

if($row3['honors'] == "true") {
$somecontent .= "Honors ".$classname."<br />";
}
else {
$somecontent .= $classname."<br />";
}
$somecontent .= "
".$rubricname."</font></strong></div></div><span style='font-size: 15px'>

Student Name: " . $student . "<br />
Division/Grade: " . $grade . "<br />
Teacher: " . $tname . "<br />
Date: " . $trow['due_date'] . "<br /></span>";


$stuid = "select rfid,id from student where id = '{$_GET['student']}'";
$stuid_result = mysql_query($stuid) or die (mysql_error());
$stuid_row = mysql_fetch_assoc($stuid_result);

//Absent / Tardy Counts
 //Count absences and tardies
              $sql_new = "select COUNT(Status) AS abscount from attendance_main where StudentID = '{$stuid_row['rfid']}' AND status = 'Absent' AND classID = '$class'";


              $sql_new_result = mysql_query($sql_new) or die(mysql_error());
              $sql_new_row = mysql_fetch_assoc($sql_new_result);
              
              $absentcount = $sql_new_row['abscount'];
              
              $sql_new2 = "select COUNT(Status) AS tardycount from attendance_main where StudentID = '{$stuid_row['rfid']}' AND status = 'Tardy' AND classID = '$class'";
              $sql_new_result2 = mysql_query($sql_new2) or die(mysql_error());
              $sql_new_row2 = mysql_fetch_assoc($sql_new_result2);
              
              $tardycount = $sql_new_row2['tardycount'];
$somecontent .= "Absent Count: ".$absentcount."<br />
Tardy Count: ".$tardycount."<br /><br />";


          //Process Convention
          $sql = "select * from rubrics_convention where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
         $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          $conventiondescrip = $row['description'];
          $somecontent .= "
<table width='100%'>
<tr><td colspan='5' class='noBorder'>
<b>Habit of Convention:</b>" . $conventiondescrip . "</td></tr>

 <tr>
   <td>E</td>
   <td>M</td>
  <td>A</td>
   <td>D</td>
   <td>Skills and Content</td>
 </tr>
";
          $convention = "select * from rubrics_convention where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $cresult = mysql_query($convention) or die(mysql_error());
          while ($crow = mysql_fetch_array($cresult)) {
    		$skill = mysql_real_escape_string($crow['skill']);

              $sql = "select * from rubrics_assessments where skill = '" . $skill . "' AND rubricid = '" . $_GET['rubric'] . "' AND studentid = '".$_GET['student']."'";
         
         
         
              $result = mysql_query($sql) or die(mysql_error());
              while ($row = mysql_fetch_array($result)) {
                  if ($row['assessment'] == "Exceeds") {
                      $grade_exceeds = "<center>X</center>";
                  } //if ($row['assessment'] == "Exceeds")
                  else {
                      $grade_exceeds = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Meets") {
                      $grade_meets = "<center>X</center>";
                  } //if ($row['assessment'] == "Meets")
                  else {
                      $grade_meets = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Approaches") {
                      $grade_approaches = "<center>X</center>";
                  } //if ($row['assessment'] == "Approaches")
                  else {
                      $grade_approaches = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Does Not Meet") {
                      $grade_doesnotmeet = "<center>X</center>";
                  } //if ($row['assessment'] == "Does Not Meet")
                  else {
                      $grade_doesnotmeet = "&nbsp;";
                  } //else
              } //while ($row = mysql_fetch_array($result))
              $somecontent .= "
 <tr>
   <td width='30'>" . $grade_exceeds . "</td>
  <td width='30'>" . $grade_meets . "</td>
    <td width='30'>" . $grade_approaches . "</td>
      <td width='30'>" . $grade_doesnotmeet . "</td>
        <td>" . $crow['skill'] . "</td>
 </tr>

";
          } //while ($crow = mysql_fetch_array($cresult))
          $sql = "select * from rubrics_habitassessments WHERE rubricid = '" . $_GET['rubric'] . "' AND studentid = '" . $_GET['student'] . "' AND habit = 'convention'";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          if ($row['assessment'] == 'Exceeds') {
              $assessment = "exceeding";
          } //if ($row['assessment'] == 'Exceeds')
          if ($row['assessment'] == 'Meets') {
              $assessment = "meeting";
          } //if ($row['assessment'] == 'Meets')
          if ($row['assessment'] == 'Approaches') {
              $assessment = "approaching";
          } //if ($row['assessment'] == 'Approaches')
          if ($row['assessment'] == 'Does Not Meet') {
              $assessment = "not meeting";
          } //if ($row['assessment'] == 'Does Not Meet')
          $somecontent .= "<tr><td colspan='5' class='noBorder'>".$student . " is " . $assessment . " standards for the Habit of Convention. </td></tr></table><span style='color: #fff'>-</span><br /><br />";
          //Process Convention
          $sql = "select * from rubrics_evidence where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          $evidencedescrip = $row['description'];
          $somecontent .= "
		  
<table width='100%'>
<tr><td colspan='5' class='noBorder'>
<b>Habit of Evidence:</b> " . $evidencedescrip . " </td></tr>
 <tr>
   <td>E</td>
   <td>M</td>
  <td>A</td>
   <td>D</td>
   <td>Skills and Content</td>
 </tr>
";
          $convention = "select * from rubrics_evidence where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $cresult = mysql_query($convention) or die(mysql_error());
          while ($crow = mysql_fetch_array($cresult)) {
              $skill = mysql_real_escape_string($crow['skill']);
              $sql = "select * from rubrics_assessments where skill = '" . $skill . "' AND rubricid = '" . $_GET['rubric'] . "' AND studentid = '".$_GET['student']."'";
              $result = mysql_query($sql) or die(mysql_error());
              while ($row = mysql_fetch_array($result)) {
                  if ($row['assessment'] == "Exceeds") {
                      $grade_exceeds = "<center>X</center>";
                  } //if ($row['assessment'] == "Exceeds")
                  else {
                      $grade_exceeds = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Meets") {
                      $grade_meets = "<center>X</center>";
                  } //if ($row['assessment'] == "Meets")
                  else {
                      $grade_meets = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Approaches") {
                      $grade_approaches = "<center>X</center>";
                  } //if ($row['assessment'] == "Approaches")
                  else {
                      $grade_approaches = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Does Not Meet") {
                      $grade_doesnotmeet = "<center>X</center>";
                  } //if ($row['assessment'] == "Does Not Meet")
                  else {
                      $grade_doesnotmeet = "&nbsp;";
                  } //else
              } //while ($row = mysql_fetch_array($result))
              $somecontent .= "
 <tr>
   <td width='30'>" . $grade_exceeds . "</td>
   <td width='30'>" . $grade_meets . "</td>
    <td width='30'>" . $grade_approaches . "</td>
     <td width='30'>" . $grade_doesnotmeet . "</td>
        <td>" . $crow['skill'] . "</td>
 </tr>
";
          } //while ($crow = mysql_fetch_array($cresult))
          $sql = "select * from rubrics_habitassessments WHERE rubricid = '" . $_GET['rubric'] . "' AND studentid = '" . $_GET['student'] . "' AND habit = 'evidence'";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          if ($row['assessment'] == 'Exceeds') {
              $assessment = "exceeding";
          } //if ($row['assessment'] == 'Exceeds')
          if ($row['assessment'] == 'Meets') {
              $assessment = "meeting";
          } //if ($row['assessment'] == 'Meets')
          if ($row['assessment'] == 'Approaches') {
              $assessment = "approaching";
          } //if ($row['assessment'] == 'Approaches')
          if ($row['assessment'] == 'Does Not Meet') {
              $assessment = "not meeting";
          } //if ($row['assessment'] == 'Does Not Meet')
          $somecontent .= "<tr><td colspan='5' class='noBorder'>".$student . " is " . $assessment . " standards for the Habit of Evidence. </td></tr></table><span style='color: #fff'>-</span><br /><br />";
          //Process Perspective
          $sql = "select * from rubrics_perspective where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          $perspectivedescrip = $row['description'];
          $somecontent .= "
<table width='100%'>
<tr><td colspan='5' class='noBorder'>
<b>Habit of Perspective:</b> " . $perspectivedescrip . " </td></tr>

 <tr>
   <td>E</td>
   <td>M</td>
  <td>A</td>
   <td>D</td>
   <td>Skills and Content</td>
 </tr>
";
          $convention = "select * from rubrics_perspective where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $cresult = mysql_query($convention) or die(mysql_error());
          while ($crow = mysql_fetch_array($cresult)) {
              $skill = mysql_real_escape_string($crow['skill']);
              $sql = "select * from rubrics_assessments where skill = '" . $skill . "' AND rubricid = '" . $_GET['rubric'] . "' AND studentid = '".$_GET['student']."'";
              $result = mysql_query($sql) or die(mysql_error());
              while ($row = mysql_fetch_array($result)) {
                  if ($row['assessment'] == "Exceeds") {
                      $grade_exceeds = "<center>X</center>";
                  } //if ($row['assessment'] == "Exceeds")
                  else {
                      $grade_exceeds = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Meets") {
                      $grade_meets = "<center>X</center>";
                  } //if ($row['assessment'] == "Meets")
                  else {
                      $grade_meets = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Approaches") {
                      $grade_approaches = "<center>X</center>";
                  } //if ($row['assessment'] == "Approaches")
                  else {
                      $grade_approaches = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Does Not Meet") {
                      $grade_doesnotmeet = "<center>X</center>";
                  } //if ($row['assessment'] == "Does Not Meet")
                  else {
                      $grade_doesnotmeet = "&nbsp;";
                  } //else
              } //while ($row = mysql_fetch_array($result))
              $somecontent .= "
 <tr>
   <td width='30'>" . $grade_exceeds . "</td>
 <td width='30'>" . $grade_meets . "</td>
    <td width='30'>" . $grade_approaches . "</td>
      <td width='30'>" . $grade_doesnotmeet . "</td>
        <td>" . $crow['skill'] . "</td>
 </tr>
";
          } //while ($crow = mysql_fetch_array($cresult))
          $sql = "select * from rubrics_habitassessments WHERE rubricid = '" . $_GET['rubric'] . "' AND studentid = '" . $_GET['student'] . "' AND habit = 'perspective'";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          if ($row['assessment'] == 'Exceeds') {
              $assessment = "exceeding";
          } //if ($row['assessment'] == 'Exceeds')
          if ($row['assessment'] == 'Meets') {
              $assessment = "meeting";
          } //if ($row['assessment'] == 'Meets')
          if ($row['assessment'] == 'Approaches') {
              $assessment = "approaching";
          } //if ($row['assessment'] == 'Approaches')
          if ($row['assessment'] == 'Does Not Meet') {
              $assessment = "not meeting";
          } //if ($row['assessment'] == 'Does Not Meet')
          $somecontent .= "<tr><td colspan='5' class='noBorder'>".$student . " is " . $assessment . " standards for the Habit of Perspective. </td></tr></table><span style='color: #fff'>-</span><br /><br />";
          //Process Connection
          $sql = "select * from rubrics_connection where rubricid = '" . $_GET['rubric'] . "'";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          $connectiondescrip = $row['description'];
          $somecontent .= "
<table width='100%'>
<tr><td colspan='5' class='noBorder'>
		  <b>Habit of Connection:</b> " . $connectiondescrip . " </td></tr>

 <tr>
   <td>E</td>
   <td>M</td>
  <td>A</td>
   <td>D</td>
   <td>Skills and Content</td>
 </tr>
";
          $convention = "select * from rubrics_connection where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $cresult = mysql_query($convention) or die(mysql_error());
          while ($crow = mysql_fetch_array($cresult)) {
              $skill = mysql_real_escape_string($crow['skill']);
              $sql = "select * from rubrics_assessments where skill = '" . $skill . "' AND rubricid = '" . $_GET['rubric'] . "' AND studentid = '".$_GET['student']."'";
              $result = mysql_query($sql) or die(mysql_error());
              while ($row = mysql_fetch_array($result)) {
                  if ($row['assessment'] == "Exceeds") {
                      $grade_exceeds = "<center>X</center>";
                  } //if ($row['assessment'] == "Exceeds")
                  else {
                      $grade_exceeds = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Meets") {
                      $grade_meets = "<center>X</center>";
                  } //if ($row['assessment'] == "Meets")
                  else {
                      $grade_meets = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Approaches") {
                      $grade_approaches = "<center>X</center>";
                  } //if ($row['assessment'] == "Approaches")
                  else {
                      $grade_approaches = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Does Not Meet") {
                      $grade_doesnotmeet = "<center>X</center>";
                  } //if ($row['assessment'] == "Does Not Meet")
                  else {
                      $grade_doesnotmeet = "&nbsp;";
                  } //else
              } //while ($row = mysql_fetch_array($result))
              $somecontent .= "
 <tr>
   <td width='30'>" . $grade_exceeds . "</td>
  <td width='30'>" . $grade_meets . "</td>
    <td width='30'>" . $grade_approaches . "</td>
      <td width='30'>" . $grade_doesnotmeet . "</td>
        <td>" . $crow['skill'] . "</td>
 </tr>
";
          } //while ($crow = mysql_fetch_array($cresult))
          $sql = "select * from rubrics_habitassessments WHERE rubricid = '" . $_GET['rubric'] . "' AND studentid = '" . $_GET['student'] . "' AND habit = 'connection'";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          if ($row['assessment'] == 'Exceeds') {
              $assessment = "exceeding";
          } //if ($row['assessment'] == 'Exceeds')
          if ($row['assessment'] == 'Meets') {
              $assessment = "meeting";
          } //if ($row['assessment'] == 'Meets')
          if ($row['assessment'] == 'Approaches') {
              $assessment = "approaching";
          } //if ($row['assessment'] == 'Approaches')
          if ($row['assessment'] == 'Does Not Meet') {
              $assessment = "not meeting";
          } //if ($row['assessment'] == 'Does Not Meet')
          $somecontent .= "<tr><td colspan='5' class='noBorder'>".$student . " is " . $assessment . " standards for the Habit of Connection. </td></tr></table><span style='color: #fff'>-</span><br /><br />";
          //Process Common Good
          $sql = "select * from rubrics_commongood where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          $commongooddescrip = $row['description'];
          $somecontent .= "
<table width='100%'>
<tr><td colspan='5' class='noBorder'>
<b>Habit of Service to the Common Good:</b> " . $commongooddescrip . " </td></tr>

 <tr>
   <td>E</td>
   <td>M</td>
  <td>A</td>
   <td>D</td>
   <td>Skills and Content</td>
 </tr>
";
          $convention = "select * from rubrics_commongood where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $cresult = mysql_query($convention) or die(mysql_error());
          while ($crow = mysql_fetch_array($cresult)) {
              $skill = mysql_real_escape_string($crow['skill']);
              $sql = "select * from rubrics_assessments where skill = '" . $skill . "' AND rubricid = '" . $_GET['rubric'] . "' AND studentid = '".$_GET['student']."'";
              $result = mysql_query($sql) or die(mysql_error());
              while ($row = mysql_fetch_array($result)) {
                  if ($row['assessment'] == "Exceeds") {
                      $grade_exceeds = "<center>X</center>";
                  } //if ($row['assessment'] == "Exceeds")
                  else {
                      $grade_exceeds = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Meets") {
                      $grade_meets = "<center>X</center>";
                  } //if ($row['assessment'] == "Meets")
                  else {
                      $grade_meets = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Approaches") {
                      $grade_approaches = "<center>X</center>";
                  } //if ($row['assessment'] == "Approaches")
                  else {
                      $grade_approaches = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Does Not Meet") {
                      $grade_doesnotmeet = "<center>X</center>";
                  } //if ($row['assessment'] == "Does Not Meet")
                  else {
                      $grade_doesnotmeet = "&nbsp;";
                  } //else
              } //while ($row = mysql_fetch_array($result))
              $somecontent .= "
 <tr>
   <td width='30'>" . $grade_exceeds . "</td>
 <td width='30'>" . $grade_meets . "</td>
    <td width='30'>" . $grade_approaches . "</td>
      <td width='30'>" . $grade_doesnotmeet . "</td>
        <td>" . $crow['skill'] . "</td>
 </tr>
";
          } //while ($crow = mysql_fetch_array($cresult))
          $sql = "select * from rubrics_habitassessments WHERE rubricid = '" . $_GET['rubric'] . "' AND studentid = '" . $_GET['student'] . "' AND habit = 'commongood'";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          if ($row['assessment'] == 'Exceeds') {
              $assessment = "exceeding";
          } //if ($row['assessment'] == 'Exceeds')
          if ($row['assessment'] == 'Meets') {
              $assessment = "meeting";
          } //if ($row['assessment'] == 'Meets')
          if ($row['assessment'] == 'Approaches') {
              $assessment = "approaching";
          } //if ($row['assessment'] == 'Approaches')
          if ($row['assessment'] == 'Does Not Meet') {
              $assessment = "not meeting";
          } //if ($row['assessment'] == 'Does Not Meet')
          $somecontent .= "<tr><td colspan='5' class='noBorder'>".$student . " is " . $assessment . " standards for the Habit of Service to the Common Good. </td></tr></table><span style='color: #fff'>-</span><br /><br />";
          //Process Collaboration
          $sql = "select * from rubrics_collaboration where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          $collaborationdescrip = $row['description'];
          $somecontent .= "
<table width='100%'>
<tr><td colspan='5' class='noBorder'>
		  <b>Habit of Collaboration:</b> " . $collaborationdescrip . " </td></tr>

 <tr>
   <td>E</td>
   <td>M</td>
  <td>A</td>
   <td>D</td>
   <td>Skills and Content</td>
 </tr>
";
          $convention = "select * from rubrics_collaboration where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $cresult = mysql_query($convention) or die(mysql_error());
          while ($crow = mysql_fetch_array($cresult)) {
              $skill = mysql_real_escape_string($crow['skill']);
              $sql = "select * from rubrics_assessments where skill = '" . $skill . "' AND rubricid = '" . $_GET['rubric'] . "' AND studentid= '".$_GET['student']."'";
              $result = mysql_query($sql) or die(mysql_error());
              while ($row = mysql_fetch_array($result)) {
                  if ($row['assessment'] == "Exceeds") {
                      $grade_exceeds = "<center>X</center>";
                  } //if ($row['assessment'] == "Exceeds")
                  else {
                      $grade_exceeds = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Meets") {
                      $grade_meets = "<center>X</center>";
                  } //if ($row['assessment'] == "Meets")
                  else {
                      $grade_meets = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Approaches") {
                      $grade_approaches = "<center>X</center>";
                  } //if ($row['assessment'] == "Approaches")
                  else {
                      $grade_approaches = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Does Not Meet") {
                      $grade_doesnotmeet = "<center>X</center>";
                  } //if ($row['assessment'] == "Does Not Meet")
                  else {
                      $grade_doesnotmeet = "&nbsp;";
                  } //else
              } //while ($row = mysql_fetch_array($result))
              $somecontent .= "
 <tr>
   <td width='30'>" . $grade_exceeds . "</td>
  <td width='30'>" . $grade_meets . "</td>
    <td width='30'>" . $grade_approaches . "</td>
     <td width='30'>" . $grade_doesnotmeet . "</td>
        <td>" . $crow['skill'] . "</td>
 </tr>
";
          } //while ($crow = mysql_fetch_array($cresult))
          $sql = "select * from rubrics_habitassessments WHERE rubricid = '" . $_GET['rubric'] . "' AND studentid = '" . $_GET['student'] . "' AND habit = 'collaboration'";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          if ($row['assessment'] == 'Exceeds') {
              $assessment = "exceeding";
          } //if ($row['assessment'] == 'Exceeds')
          if ($row['assessment'] == 'Meets') {
              $assessment = "meeting";
          } //if ($row['assessment'] == 'Meets')
          if ($row['assessment'] == 'Approaches') {
              $assessment = "approaching";
          } //if ($row['assessment'] == 'Approaches')
          if ($row['assessment'] == 'Does Not Meet') {
              $assessment = "not meeting";
          } //if ($row['assessment'] == 'Does Not Meet')
          $somecontent .= "<tr><td colspan='5' class='noBorder'>".$student . " is " . $assessment . " standards for the Habit of Collaboration. </td></tr></table><span style='color: #fff'>-</span><br /><br />";
          //Process Ethical Behavior
          $sql = "select * from rubrics_ethicalbehavior where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          $ethicalbehaviordescrip = $row['description'];
          $somecontent .= "
<table width='100%'>
<tr><td colspan='5' class='noBorder'>
<b>Habit of Ethical Behavior:</b> " . $ethicalbehaviordescrip . " </td></tr>

 <tr>
   <td>E</td>
   <td>M</td>
  <td>A</td>
   <td>D</td>
   <td>Skills and Content</td>
 </tr>
";
          $convention = "select * from rubrics_ethicalbehavior where rubricid = '" . $_GET['rubric'] . "' AND skill != ''";
          $cresult = mysql_query($convention) or die(mysql_error());
          while ($crow = mysql_fetch_array($cresult)) {
              $skill = mysql_real_escape_string($crow['skill']);
              $sql = "select * from rubrics_assessments where skill = '" . $skill . "' AND rubricid = '" . $_GET['rubric'] . "' AND studentid = '".$_GET['student']."'";
              $result = mysql_query($sql) or die(mysql_error());
              while ($row = mysql_fetch_array($result)) {
                  if ($row['assessment'] == "Exceeds") {
                      $grade_exceeds = "<center>X</center>";
                  } //if ($row['assessment'] == "Exceeds")
                  else {
                      $grade_exceeds = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Meets") {
                      $grade_meets = "<center>X</center>";
                  } //if ($row['assessment'] == "Meets")
                  else {
                      $grade_meets = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Approaches") {
                      $grade_approaches = "<center>X</center>";
                  } //if ($row['assessment'] == "Approaches")
                  else {
                      $grade_approaches = "&nbsp;";
                  } //else
                  if ($row['assessment'] == "Does Not Meet") {
                      $grade_doesnotmeet = "<center>X</center>";
                  } //if ($row['assessment'] == "Does Not Meet")
                  else {
                      $grade_doesnotmeet = "&nbsp;";
                  } //else
              } //while ($row = mysql_fetch_array($result))
              $somecontent .= "
 <tr>
   <td width='30'>" . $grade_exceeds . "</td>
  <td width='30'>" . $grade_meets . "</td>
    <td width='30'>" . $grade_approaches . "</td>
      <td width='30'>" . $grade_doesnotmeet . "</td>
        <td>" . $crow['skill'] . "</td>
 </tr>
";
          } //while ($crow = mysql_fetch_array($cresult))
          $sql = "select * from rubrics_habitassessments WHERE rubricid = '" . $_GET['rubric'] . "' AND studentid = '" . $_GET['student'] . "' AND habit = 'ethicalbehavior'";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          if ($row['assessment'] == 'Exceeds') {
              $assessment = "exceeding";
          } //if ($row['assessment'] == 'Exceeds')
          if ($row['assessment'] == 'Meets') {
              $assessment = "meeting";
          } //if ($row['assessment'] == 'Meets')
          if ($row['assessment'] == 'Approaches') {
              $assessment = "approaching";
          } //if ($row['assessment'] == 'Approaches')
          if ($row['assessment'] == 'Does Not Meet') {
              $assessment = "not meeting";
          } //if ($row['assessment'] == 'Does Not Meet')
          $somecontent .= "<tr><td colspan='5' class='noBorder'>".$student . " is " . $assessment . " standards for the Habit of Ethical Behavior. </td></tr></table><span style='color: #fff'>-</span><br /><b>Comments</b><br />";
          $sql = "select * from rubrics_comments where rubricid = '" . $_GET['rubric'] . "' AND studentid = '" . $_GET['student'] . "'";
          $result = mysql_query($sql) or die(mysql_error());
          $row = mysql_fetch_assoc($result);
          $somecontent .= $row['comments'] . "

</body></html>";

    $filename2 = 'dompdf/www/rubrics/' . $_GET['rubric'] . '_' . $_GET['student'] . '_comments.php';

$somecontent2 = $row['comments'];
 if (!$handle2 = fopen($filename2, 'w+')) {
              echo "Cannot open file ($filename2)";
              exit;
          } //if (!$handle = fopen($filename, 'w+'))
          // Write $somecontent to our opened file.
          if (fwrite($handle2, $somecontent2) === false) {
              echo "Cannot write to file ($filename)";
              exit;
          } //if (fwrite($handle, $somecontent) === false)
          //echo "Success, wrote ($somecontent) to file ($filename)";
          //echo "Success!";
          fclose($handle2);
		  
		  
          // Let's make sure the file exists and is writable first.
          // In our example we're opening $filename in append mode.
          // The file pointer is at the bottom of the file hence
          // that's where $somecontent will go when we fwrite() it.
          if (!$handle = fopen($filename, 'w+')) {
              echo "Cannot open file ($filename)";
              exit;
          } //if (!$handle = fopen($filename, 'w+'))
          // Write $somecontent to our opened file.
          if (fwrite($handle, $somecontent) === false) {
              echo "Cannot write to file ($filename)";
              exit;
          } //if (fwrite($handle, $somecontent) === false)
          //echo "Success, wrote ($somecontent) to file ($filename)";
          //echo "Success!";
          fclose($handle);

     echo '<a href="dompdf/dompdf.php?base_path=www%2Frubrics%2F&input_file=' . $_GET['rubric'] . '_' . $_GET['student'] . '.php&rubric_name='.$studentRow['first_name']."".$studentRow['last_name'].'_'.$row2['name'].'_'.date("m-d-y").'"><img src="download-student.png"></a><br />Download '.$studentRow['first_name'].' '.$studentRow['last_name'].'
';

?>