<?php

if(isset($_GET['grade'])){
	$navgrade = $_GET['grade'];
}
elseif(isset($grade)){
	$navgrade = $grade;
}
else{
	$navgrade = '1';
}

if(isset($_GET['subj'])){
	$navsubject = $_GET['subj'];
}

elseif(isset($subjectid)){
	$navsubject = $subjectid;
}

else{
	$firstsubj = mysql_fetch_row(mysql_query("SELECT elem_subjects.subjectid FROM elem_subjects ORDER BY subject_name ASC LIMIT 1"));
	$navsubject = $firstsubj[0];
}



$navbar = "<ul id='nav'>
            <li><a href='/elementary/index.php'>Home</a></li>
	<!--	<li><a href='/elementary/attendance'>Attendance</a></li>	-->
            <li><a href='/elementary/assessments'>Assessments</a>
				<ul ><li><a href='#'>Template Setup";

// Check if Template Setup is locked
$today = date("Y-m-d");
if(($today >= $prefarray['setting_lock_date']) && ($prefarray['setting_lock_override'] != 1) && ( strpos($user[flags], "a") === false)){
	$navbar .=	"<img style='float:right' src='/elementary/imgs/lock.png' height='10px'></a></li></ul>";
}
else{ 
	$navbar .=	"
					</a><ul style='z-index:100'>
	<!--			<li><a href='/elementary/admin/assessments.php'>Assessment Options</a></li>		-->
					<li><a href='/elementary/admin/subjects.php?subj=".$navsubject."&grade=".$navgrade."'>Course Descriptions</a></li>
					<li><a href='/elementary/admin/headings.php?subj=".$navsubject."&grade=".$navgrade."'>Skill Headings</a></li>
					<li><a href='/elementary/admin/skills.php?subj=".$navsubject."&grade=".$navgrade."'>Skill Descriptions</a></li>
				</ul></li></ul>";
}


$navbar .=	"</li><li><a href='/elementary/conferences'>Conferences</a></li>
";

// Show Reader Panel
if( ((strpos($user[flags], "a") !== false)) || ((strpos($user[flags], "r") !== false))){
	$navbar .= "	<li><a href='/elementary/admin/reader.php".$_SESSION['reader_url']."'>Reader Panel</a></li>";
}

// Show Admin options
if( (strpos($user[flags], "a")) !== false){
	$navbar .= "	<li><a href='#'>Admin</a>
			<ul style='z-index:100'>
				<li><a href='/elementary/admin/downloads.php'>Bulk Download</a></li>";
				
// Masquerading				
	$navbar .= "	<li><a href='#'>Masquerade</a>
					<ul style='z-index:100;width:200px'><li><form name='masqform' id='masqform' method='POST'><a>	
						<select name='masq' onChange=\"document.masqform.submit()\">
						<option value='".$user['name']."'>Select User:</option>
						<option disabled='disabled'><br></option>";
	foreach($elem_teacher_array as $masqid => $masqarray){
			$navbar .=	"<option value='$masqid'>".$masqarray['fname']." ".$masqarray['lname']."</option>";	
	}
	
	$navbar .= "	</select></a></form></li>
					</ul></li>";
					
	$navbar .= "<li><a href='/elementary/admin/users.php?page=roles'>User/Class Info</a></li>
				<li><a href='/elementary/admin/admin.php'>Options</a></li>
				<li><a href='/elementary/admin/logs.php'>Logs</a></li>
			</ul></li>";
}          



$navbar .= "<li style='float:right;'><a href='?act=logout'>Logout</a></li>
			";


// $selectyear = "<li style='float:right;'><a href='#'>".$_SESSION['year']."</a>
// 			<ul style='z-index:100'>";
// 
// $selectyear = "<li><form name='yearform' id='yearform' method='POST'><select name='year' onChange=\"document.yearform.submit()\">";
// foreach($yeararray as $years){
// 	$selectyear .= "<option value='$years'>".($years-1)."-$years</option>";
// }
// $selectyear .="</select></form>";
// $selectyear = str_replace("<option value='".$_SESSION['year']."'>","<option value='".$_SESSION['year']."' selected='selected'>",$selectyear);			
// $selectyear 	.= "</li></ul></li>";
// $navbar .=  $selectyear;			
			

			
if(isset($_SESSION['masq'])) {
	$navbar .= "<li style='float:right;'><a href='?act=unmasq'>Stop Masquerading</a></li>
			";
}

$navbar .= "</ul>";

echo $navbar;

?>
<html>
