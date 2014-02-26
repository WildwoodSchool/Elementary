<?php

// STATIC ARRAYS // STATIC ARRAYS // STATIC ARRAYS // STATIC ARRAYS // STATIC ARRAYS 
//
// Grade numbers
$gradenumbers = array(
	0 => "Kindergarten",
	1 => "1st Grade",
	2 => "2nd Grade",
	3 => "3rd Grade",
	4 => "4th Grade",
	5 => "5th Grade",
);

$elem_divisions = array(
	1 => "Pods",
	2 => "2nd Grade",
	3 => "3rd Grade",
	4 => "4th Grade",
	5 => "5th Grade",
);

// Status Values
$statusvalues = array(
	0 => "In Progress",
	1 => "Ready for Reader Review",
	2 => "Needs Teacher Changes",
	3 => "Final",
);

// Status badges
$statusbadge = array(
	1 => "<img src='/elementary/imgs/ready-badge.png' style='position:absolute;right:-5px;top:-5px;'>",
	2 => "<img src='/elementary/imgs/changes-badge.png' style='position:absolute;right:-5px;top:-5px;'>",
	3 => "<img src='/elementary/imgs/complete-badge.png' style='position:absolute;right:-5px;top:-5px;'>",
);

// Status icons
$statusicon = array(
	0 => "<img src='/elementary/imgs/blank.png' >",
	1 => "<img src='/elementary/imgs/ready.png' >",
	2 => "<img src='/elementary/imgs/changes.png' >",
	3 => "<img src='/elementary/imgs/complete.png' >",
);

// Grey status icons
// (complete still left green)
$greystatusicon = array(
	0 => "<img src='/elementary/imgs/blank-grey.png' >",
	1 => "<img src='/elementary/imgs/ready-grey.png' >",
	2 => "<img src='/elementary/imgs/changes-grey.png' >",
	3 => "<img src='/elementary/imgs/complete.png' >",
);

// Light status icons
$lightstatusicon = array(
	0 => "<img src='/elementary/imgs/blank-light.png' >",
	1 => "<img src='/elementary/imgs/ready-light.png' >",
	2 => "<img src='/elementary/imgs/changes-light.png' >",
	3 => "<img src='/elementary/imgs/complete-light.png' >",
);

// Semester Names
$semesters = array(
	1 => "Winter",
	2 => "Spring",
);

// Skill semester options
$skillsemesters = array(
	0 => "Full Year",
	1 => "Winter Only",
	2 => "Spring Only",
);

// User rolls
$roles_array = array(
	"" => "",
	"r" => "Reader",
	"a" => "Admin",
	"d" => "Disabled",
);

// Campus Names
$campus_array = array(
	"1" => "Elementary",
	"2" => "Middle/Upper",
);


if(!isset($_SESSION['yeararray'])){
	$filename = "http://connections.wildwood.org/elementary/includes/yearfile.txt";
	$_SESSION['yeararray'] = file($filename, FILE_IGNORE_NEW_LINES);	
	$yeararray = $_SESSION['yeararray'];
}
else{
	$yeararray = $_SESSION['yeararray'];
}


// MYSQL ARRAYS // MYSQL ARRAYS // MYSQL ARRAYS // MYSQL ARRAYS // MYSQL ARRAYS
// 
// Site Preferences
if(!isset($_SESSION['site_prefs'])){
	$prefsql = "SELECT * FROM elem_site_prefs";
	$prefresult = mysql_query($prefsql) or die (mysql_error());
	while($prefrow = mysql_fetch_array($prefresult)){
		$prefarray[($prefrow['pref_name'])] .= $prefrow['pref_value'];
	}
	$_SESSION['site_prefs'] = $prefarray;
}
else{
	$prefarray = $_SESSION['site_prefs'];
}

// Assessment Names and Abbrs
if(!isset($_SESSION['assess_names']) || !isset($_SESSION['assess_abbrs'])){
	$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '1' OR set_id = '0' ORDER BY assess_sort DESC";
	$assessresult = mysql_query($assesssql) or die (mysql_error());
	while($assessrow = mysql_fetch_array($assessresult)){
		$assess_name_array[($assessrow['assessid'])] .= $assessrow['assess_name'];
		$assess_abbr_array[($assessrow['assessid'])] .= $assessrow['assess_abbr'];
	}
	$_SESSION['assess_names'] = $assess_name_array;
	$_SESSION['assess_abbrs'] = $assess_abbr_array;
}
else{
	$assess_name_array = $_SESSION['assess_names'];
	$assess_abbr_array = $_SESSION['assess_abbrs'];
}

// Subjects Names and Abbrs
if(!isset($_SESSION['subject_names']) || !isset($_SESSION['subject_abbrs'])){
	$subjsql = "SELECT * FROM elem_subjects ORDER BY subject_sort ASC";
	$subjresult = mysql_query($subjsql) or die (mysql_error());
	while($subjrow = mysql_fetch_array($subjresult)){
		$subj_name_array[($subjrow['subjectid'])] .= $subjrow['subject_name'];
		$subj_abbr_array[($subjrow['subjectid'])] .= $subjrow['subject_abbr'];
	}
	$_SESSION['subject_names'] = $subj_name_array;
	$_SESSION['subject_abbrs'] = $subj_abbr_array;
}
else{
	$subj_name_array = $_SESSION['subject_names'];
	$subj_abbr_array = $_SESSION['subject_abbrs'];
}

// Add Social/Emotional Class Notes to Subject Names
$full_abbr_array = array(0 => $prefarray['social_notes_abbr'],);
foreach ($subj_abbr_array as $key => $abbr){
	$full_abbr_array[$key] .= $abbr;
}

$full_name_array = array(0 => $prefarray['social_notes_name'],);
foreach ($subj_name_array as $key => $name){
	$full_name_array[$key] .= $name;
}

// Teacher List Arrays
if(!isset($_SESSION['elem_teacher_array'])){
	$teachersql = "SELECT * FROM teacher_accounts WHERE campus = '1' ORDER BY fname ASC ";
	$teacherresult = mysql_query($teachersql) or die (mysql_error());
	while($teacherrow = mysql_fetch_array($teacherresult)){
		$_SESSION['elem_teacher_array'][($teacherrow['username'])] = array(
			"name"	=> $teacherrow['username'],
			"id"	=> $teacherrow['id'],
			"flags"	=> $teacherrow['flags'],
			"fname"	=> $teacherrow['fname'],
			"lname"	=> $teacherrow['lname'],
		);
	}
	$elem_teacher_array = $_SESSION['elem_teacher_array'];
}
else{
	$elem_teacher_array = $_SESSION['elem_teacher_array'];
}


?>