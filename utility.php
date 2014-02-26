<?php
session_start();


// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info] 
require ("includes/usercheck.php");

// Connect to elementary DB
require ("includes/database.php");
$i = 0;
echo "<table><tr><td>STUID</td><td>OLD FNAME</td><td>OLD LNAME</td><td>NEW FNAME</td><td>NEW LNAME</td><td>COUNT</td></tr>";
$updatesql = "SELECT * FROM k12";
$updateresult = mysql_query($updatesql) or die (mysql_error());
while($updaterow = mysql_fetch_array($updateresult)){
	$i = ($i + 1);
	$stuid = mysql_escape_string($updaterow['stuid']);
	$fname = mysql_escape_string($updaterow['firstname']);
	$lname = mysql_escape_string($updaterow['lastname']);
	$getsql = "SELECT * FROM student WHERE id = '$stuid'";
	$getresult = mysql_query($getsql) or die (mysql_error());
	$getrow = mysql_fetch_array($getresult);
	$changesql = "UPDATE student SET fname = '$fname', lname = '$lname' WHERE id = '$stuid'";
	$changequery = mysql_query($changesql) or die (mysql_error());
	
	echo "<tr><td>".$stuid."</td><td>".$getrow['fname']."</td><td>".$getrow['lname']."</td><td>".$fname."</td><td>".$lname."</td><td>".$i."</td></tr>";
}
echo "</table>";
	

	
	
	
?>