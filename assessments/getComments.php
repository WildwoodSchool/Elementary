<?php

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info] 
require ("../includes/usercheck.php");

// Connect to elementary DB
require ("../includes/database.php");

// Get Array Values
require ("../includes/arrays.php");


$wcommentsql = "SELECT * FROM elem_stu_comments WHERE stuid = '".$_GET['stuid']."' AND subjectid = '".$_GET['subj']."' AND semester = '1'";
$wcommentresult = mysql_query($wcommentsql) or die (mysql_error());
$wcommentrow = mysql_fetch_array($wcommentresult);
$wcomments = stripslashes($wcommentrow['comments']);

echo $wcomments;

?>