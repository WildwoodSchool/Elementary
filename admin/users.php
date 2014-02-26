<?php


// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info]; 
require ("../includes/usercheck.php");

// Make sure to use current DB
// End of script will swap back temp_year and year
$_SESSION['temp_year'] = $_SESSION['year'];
$_SESSION['year'] = $_SESSION['real_year'];

// Connect to elementary DB
require ("../includes/database.php");
// echo $the_db.'<br>year: '.$_SESSION['year'].'<br>temp: '. $_SESSION['temp_year'].'<br>real: '.$_SESSION['real_year'];

$_SESSION['site_prefs'] = null;
// Get Array Values
include ("../includes/arrays.php");


// Check that user is admin
if((strpos($user[flags], "a")) === false){    
		header ("Location: /elementary/index.php");
}

// Add and Remove Users 
foreach($_POST['remove'] as $removeme){
	$delete_sql = $_SESSION['user_delete'][$removeme];
	mysql_query($delete_sql) or die (mysql_error());

}

foreach($_POST['add'] as $addme){
	$insert_sql = $_SESSION['user_insert'][$addme];
	mysql_query($insert_sql);
}

if(isset($_POST['facupdate'])){
	foreach($_POST['update'] as $key => $newrole){
		if($key != $user['name']){
			$update_sql = "UPDATE teacher_accounts SET flags = '".$newrole['role']."', campus = '".$newrole['campus']."' WHERE username = '$key'";
		}
		else{
			$update_sql = "UPDATE teacher_accounts SET campus = '".$newrole['campus']."' WHERE username = '$key'";
		}	
		mysql_query($update_sql);
	}
}

$_SESSION['elem_teacher_array'] = null;
include ("../includes/arrays.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Wildwood School :: Online Evaluation System</title>
    <link href="/elementary/css/main.css" media="screen" rel="stylesheet" type="text/css" />    <script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
    <script src='/elementary/js/spectrum/spectrum.js'></script>
    <link rel='stylesheet' href='/elementary/js/spectrum/spectrum.css' />
    
    <script type="text/javascript" src="/js/jquery-ui-1.8.21.custom.min.js"></script>
    <script type="text/javascript" src="/js/ajax.js"></script>
    <script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/smoothness/jquery-ui.css"    />
    <script type="text/javascript" src="/elementary/js/jquery.tablednd.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="/elementary/css/tablednd.css"    />
    <script type="text/javascript">
		tinyMCE.init({
			content_css : "/css/tiny_mce_custom_content.css",
			theme_advanced_font_sizes: "10px,12px,13px,14px,16px,18px,20px",
			font_size_style_values : "10px,12px,13px,14px,16px,18px,20px",
			mode : "exact",
			elements : "elm1",
			theme : "advanced",
			plugins : "tinyautosave,autosave,spellchecker,paste",
			theme_advanced_buttons1 : "bold,underline,italic,bullist,numlist,spellchecker,autosave",
			theme_advanced_buttons3 : "",
			spellchecker_languages : "+English=en,Spanish=es",
			valid_styles : {'*' : 'color,font-size,font-weight,font-style,text-decoration'},
			paste_use_dialog : true,
			paste_auto_cleanup_on_paste : true,
			theme_advanced_buttons1_add: "tinyautosave"
		});
		</script>
		

	
	<script type="text/javascript">


		var changes = false;
		function changeFunc(changes){
			if (changes == true){
				function closeEditorWarning(){
					return 'Are you sure you want to leave? You have unsaved changes.'
				}
				window.onbeforeunload = closeEditorWarning;
			}
			else{
				window.onbeforeunload = null;
			}
		console.log (changes);
		}

	</script>

<script type='text/javascript'>
    $(document).ready(function(){
        $('#datepicker').click(function(){
            $('#datepicker').datepicker().datepicker('show');
        });
        
	$('#notadded-toggle').click(function(){
			$('#notadded-list').toggle();
			$('#notadded-up').toggle();
			$('#notadded-down').toggle();
		});

    });
    
    
</script>

<body>

<div id="wrapper">
    <div id="header">

        <div id="userPanel">


        </div>
        <img src="/images/assessmentsLogo.png" style="padding:10px 20px 30px 20px;">
<?php

// Echo Navbar
include ("../includes/navbar.php");
?>
    <div id="content">
<?php   

echo "<h3>Manage User Data</h3>";

$subnav = "<ul id='subnav'>
<li><a href='?page=roles'>Faculty/Staff Roles</a></li>
<li><a href='?page=sync'>Active Directory Sync</a></li>
<li><a href='?page=import'>Import Student Data</a></li>
</ul>";
$subnav = str_replace("<li><a href='?page=".$_GET['page']."'>","<li class='current'><a href='?page=".$_GET['page']."'>",$subnav);
echo $subnav;

if(($_GET['page'] == 'roles')){
	$usersql = "SELECT * FROM teacher_accounts ORDER BY lname";
	$userresult = mysql_query($usersql) or die (mysql_error());
	while ($userrow = mysql_fetch_array($userresult)){
		$db_fac[] = $userrow['username'];
		$db_allinfo[] = $userrow;
	}

	foreach($db_allinfo as $key => $val){
		$roll_select = null;
		$fac_select = null;
		$fac_table .=  "<tr><td>".$db_allinfo[$key]['fname']."</td><td>".$db_allinfo[$key]['lname']."</td><td>".$db_allinfo[$key]['username']."</td><td>".$db_allinfo[$key]['id']."</td><td>";
		$camp_select = "<select style='width:120px' name='update[".$db_allinfo[$key]['username']."][campus]'>";
		foreach($campus_array as $campid => $campname){
			$camp_select .=  "<option value='$campid'>$campname</option>";
		}
		$camp_select .= "</select>";
		$camp_select = str_replace("<option value='".$db_allinfo[$key]['campus']."'>","<option value='".$db_allinfo[$key]['campus']."' selected='selected'>", $camp_select);
		$fac_table .= $camp_select;
		$fac_table .=  "</td><td align='center'>";
		if($db_allinfo[$key]['username'] == $user['name']){
			$roll_select = $roles_array[($db_allinfo[$key]['flags'])];
		}
		else{
			$roll_select = "<select style='width:80px' name='update[".$db_allinfo[$key]['username']."][role]'>";
			foreach($roles_array as $flag => $full){
				$roll_select .=  "<option value='$flag'>$full</option>";
			}
			$roll_select .= "</select>";
			$roll_select = str_replace("<option value='".$db_allinfo[$key]['flags']."'>","<option value='".$db_allinfo[$key]['flags']."' selected='selected'>", $roll_select);
		}
		$fac_table .= $roll_select;
		$fac_table .=  "</td></tr>";
			
	}
		echo "<form method='post'>
			<table class='table' style='font-size:12pt;width:90%;'><tr><td width='15%'><b>First Name</b></td><td width='15%'><b>Last Name</b></td><td width='15%'><b>Username</b></td><td width='15%'><b>Employee ID</b></td><td width='15%' align='center'><b>Campus</b></td><td width='15%' align='center'><b>Role</b></td>
			".$fac_table."
			</table><br><br><input type='submit' name='facupdate' value='Save Changes'></form>";
// 		echo var_dump($_POST['update']);
}


if(($_GET['page'] == 'sync') && ($_GET['success'] != 1)){

	// adLDAP directory listing
	require ("../ldap/adLDAP.php");
	try {
		$adldap = new adLDAP();
	}
	catch (adLDAPException $e) {
		echo $e; exit();   
	}

	$folders = $adldap->folder_list(array('Managed'));
	$seniors = $_GET['grade'];
	$max_year = ($seniors + 12);
	$_SESSION['sql'] = null;
	foreach($folders as $key=> $user){

		$objectclass = $user['objectclass'];
		$distinguishedname = $user['distinguishedname'];
		$samaccountname = $user['samaccountname'];	
		if($objectclass[3] == "user"){
			$insert = null;
			$aduser = $samaccountname[0];
			$userinfo = $adldap->user_info($aduser,  array("samaccountname","givenname", "sn", "distinguishedname","mail","employeeid"));
			$insert['fname'] =  mysql_real_escape_string($userinfo[0]["givenname"][0]);
			$insert['lname'] = mysql_real_escape_string($userinfo[0]["sn"][0]);
			$insert['username'] = mysql_real_escape_string($userinfo[0]["samaccountname"][0]);
			$insert['id'] = mysql_real_escape_string($userinfo[0]["employeeid"][0]);
			$location = str_replace("OU=","", $userinfo[0]["distinguishedname"][0]);
			$location = explode(",", $location);
			if($location[2] == "Student Accounts"){
				$ad_stu[] = mysql_real_escape_string($userinfo[0]["samaccountname"][0]);
				$insert['email'] = mysql_real_escape_string($userinfo[0]["mail"][0]);
	// 			$insert['role'] = "stu";
				$insert['grad_year'] = str_replace("ClassOf","",$location[1]);
				$insert['grade'] = abs($insert['grad_year'] - $max_year);
				$allinfo[] = $insert;
				$allstu[] = $insert;

			}
			else{
	// 			$insert['role'] = "fac";
				$insert['employeeID'] = mysql_real_escape_string($userinfo[0]["employeeid"][0]);

				if($location[1] == "Secondary"){
					$insert['campus'] = "2";
					$ad_fac[] = mysql_real_escape_string($userinfo[0]["samaccountname"][0]);
					$allinfo[] = $insert;
					$allfac[] = $insert;

				}
				if($location[1] == "Elementary"){
					$insert['campus'] = "1";
					$ad_fac[] = mysql_real_escape_string($userinfo[0]["samaccountname"][0]);		
					$allinfo[] = $insert;
					$allfac[] = $insert;

				}
				if($location[1] == "Roaming"){
					$insert['campus'] = "2";
					$ad_fac[] = mysql_real_escape_string($userinfo[0]["samaccountname"][0]);		
					$allinfo[] = $insert;
					$allfac[] = $insert;

				}
			}

			
		}
	}

// echo var_dump($allstu);

	// Create array of teacher accounts
	$usersql = "SELECT * FROM teacher_accounts";
	$userresult = mysql_query($usersql) or die (mysql_error());
	while ($userrow = mysql_fetch_array($userresult)){
		$db_fac[] = $userrow['username'];
		$db_allinfo[] = $userrow;
	}


	// Teachers not in database
	$_SESSION["user_insert"] = null;
	$notadded = null;
	$removed = null;
	$user_insert = null;
	$not_in_db = array_diff($ad_fac, $db_fac);
	sort($not_in_db);
	foreach($not_in_db as $key => $val){
		foreach($allfac as $allkey => $allval){
			 if($allval['username'] == $val){
				if($allval['id'] != null){
					$db = implode(', ',array_keys($allval));	
					$insertvals = implode("', '",$allval);
					$user_insert[$key] .= "INSERT INTO teacher_accounts (".$db.") VALUES ('".$insertvals."')";
					$_SESSION["user_insert"] = $user_insert;
					$added .= "<tr><td align='center'><input type='checkbox' name='add[]' value='$key' checked='checked'></td><td>".$val."</td><td>".$allval['fname']."</td><td>".$allval['lname']."</td><td>".$campus_array[($allval['campus'])]."</td><td>".$allval['id']."</td></tr>";
				}
				else{
					$notadded .= "<tr><td></td><td>".$val."</td><td>".$allval['fname']."</td><td>".$allval['lname']."</td><td>".$campus_array[($allval['campus'])]."</td><td></td></tr>";
				}	
			}
		}
	}


	$not_in_ad = array_diff($db_fac, $ad_fac);
	$_SESSION["user_delete"] = null;
	$user_delete = null;

	foreach($not_in_ad as $key => $val){
		$deleteme =  $db_allinfo[$key]['username'];
		$user_delete[$key] .= "DELETE FROM teacher_accounts WHERE username = '$deleteme'";
		$_SESSION["user_delete"] = $user_delete;
		$removed .= "<tr><td align='center'><input type='checkbox' name='remove[]' value='$key' checked='checked'></td><td>".$db_allinfo[$key]['username']."</td><td>".$db_allinfo[$key]['fname']."</td><td>".$db_allinfo[$key]['lname']."</td><td>".$campus_array[($db_allinfo[$key]['campus'])]."</td><td>".$db_allinfo[$key]['id']."</td></tr>";
	
	}

	echo "<form method='post' action='?page=roles'>";

	echo "<br><b>The following Active Directory users will be added:</b><br>";
	if($added != null){
		echo "<br><table class='table' style='font-size:12pt;width:90%;'><tr><td width='60px' align='center'><b>Add</b></td><td width='150px'><b>Username</td><td width='130px'><b>First Name</td><td width='150px'><b>Last Name</td><td width='120px'><b>Campus</td><td><b>Employee ID</td></tr>
		".$added.
		"</table><br>";

	}
	else{
		echo "&nbsp;&nbsp;There are no users in Active Directory that can be added at this time.<br>

		<br>";
	}	
	if($notadded != null){
		echo "<div id='notadded-toggle'>
		<img id='notadded-up' style='display: inline;' src='/images/assessments/arrow-up.png'>
		<img id='notadded-down' style='display:none;' src='/images/assessments/arrow-down.png'>
		Warning - Some users cannot be added. Click for more info.
		</div>
		<div id='notadded-list' style='display:none;'>
		";
		echo "<br>The following Active Directory users do not have Employee IDs and cannot be added. This must be fixed in Active Directory.</b><br><br>
		<table class='table' style='font-size:12pt;width:90%;'><tr><td width='60px'></td><td width='150px'><b>Username</td><td width='130px'><b>First Name</td><td width='150px'><b>Last Name</td><td width='120px'><b>Campus</td><td><b>Employee ID</b></td></tr>";
		echo $notadded;
	
		echo "</table></div>";
	}



	echo "<br><b>The following users are not in Active Directory and will be removed:</b><br>";
	if($removed != null){
		echo "<br>
		<table class='table' style='font-size:12pt;width:90%;'><tr><td width='60px' align='center'><b>Remove</b></td><td width='150px'><b>Username</td><td width='130px'><b>First Name</td><td width='150px'><b>Last Name</td><td width='120px'><b>Campus</td><td><b>Employee ID</td></tr>";
		echo $removed;
		echo "</table>";
	}
	else{
		echo "&nbsp;&nbsp;There are no users present that do not exist in Active Directory.<br>

		<br>";
	}	
	echo "<br><br><div style='margin-bottom:150px'><input type='submit' name='sync' value='Sync Accounts'></form></div>";

}

if(($_GET['page'] == 'import')){
	

	function underscore_values($array_id, $array){
		foreach($array as $key => $value){
			if($key == $array_id){
				$value = str_replace(" ","_",$value);
				$value = mysql_real_escape_string($value);	
			}
			$array[$key] = $value;
			$values = implode("','",$array);
		}
		return $values;

	}
	if(isset($_FILES["file"])){
		//Get file
		$i = 0;
		if ($_FILES["file"]["error"] > 0){
		  echo "Error: " . $_FILES["file"]["error"] . "<br />";
		}
		else{
			$upfile = $_FILES["file"]["tmp_name"];
			echo "Uploaded: " . $_FILES["file"]["name"]. "<br />";
	// 		echo "Type: " . $_FILES["file"]["type"] . "<br />";
	// 		echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
	// 		echo "Stored in: " . $_FILES["file"]["tmp_name"] . "<br />";
	// 		echo $upfile . "</br></br>";

			$handle = fopen("$upfile", "r");
	
			while (($data = fgetcsv($handle, 5000, ",")) !== FALSE){
				$csv[] = ($data);	
			}

		// 	echo $csv[0][0]." ".$csv[0][1];

		// For student_to_class csv  	
			if (($csv[0][0] == "Class ID") && ($csv[0][1] == "Student ID")){
				$table = "student_to_class";
				foreach($csv as $row => $entries){
					if($row != 0){
						$i++;
						$sql[] = "INSERT INTO $table (classid, stuid) VALUES ('".underscore_values(0,$entries)."')";
					}
				}
			}

		// For teacher_to_class csv
			elseif (($csv[0][0] == "Record ID") && ($csv[0][1] == "Class ID")){
				$table = "teacher_to_class";
				foreach($csv as $row => $entries){
					if($row != 0){
						$i++;
						$sql[] = "INSERT INTO $table (empid, classid) VALUES ('".underscore_values(1,$entries)."')";
					}
				}
			}

		// For Classes
			elseif (($csv[0][0] == "Class ID") && ($csv[0][1] == "Course name")){
				$table = "class";
				foreach($csv as $row => $entries){
					if($row != 0){
						$i++;
						$sql[] = "INSERT INTO $table (id, description, name) VALUES ('".underscore_values(0,$entries)."','".mysql_real_escape_string($entries[0])."')";
					}
				}
			}

		// For student accounts
			elseif (($csv[0][0] == "Last name") && ($csv[0][1] == "First name") && ($csv[0][2] == "Nickname") && ($csv[0][3] == "Student ID") && ($csv[0][4] == "Class of") && ($csv[0][5] == "Current Grade")){
				$table = "student";
				foreach($csv as $row => $entries){
					$grade = intval(str_replace("Grade ", "", $entries[5]));
					if($grade == "Kindergarten"){
						$grade == "0";
					}
					if(($row != 0) && ($grade < 6)){
						$i++;
						$sql[] = "INSERT INTO $table (id, fname, lname, stuid, grade) VALUES ('".mysql_real_escape_string($entries[3])."','".mysql_real_escape_string($entries[1])."','".mysql_real_escape_string($entries[0])."','".mysql_real_escape_string($entries[4])."','$grade')";
					}
				}
			}	
			else{
				echo "The uploaded CSV was not recognized as one of the accepted formats";
			}
			if(!empty($table)){
				$update_date = date("m/d/Y H:i:s");
				$pref_name = $table."_update";
				$updated_date = "UPDATE elem_site_prefs SET pref_value = '$update_date' WHERE pref_name = '".$pref_name."'";
				$prefarray[$pref_name] = $update_date;
				$upresult = mysql_query($updated_date) or die (mysql_error());
				
				$trunc = "TRUNCATE TABLE $table";
				$result = mysql_query($trunc) or die (mysql_error());

				foreach($sql as $insert){
// 					echo $insert."<br>";

					mysql_query($insert) or die (mysql_error());
				}
	
				echo "Updated $i entries in table '".$table."'<br><br>";
			}

		}

	}

// Ask for file
	echo "<style type='text/css'>
	#container {
		width:800px;
		margin:auto;
		padding:10px; 
		font:normal 18px Arial;
	}


	</style>


	</head>
	<body>
	<script type='text/javascript'>
	$(document).ready(function() {
		$('#class').click(function(){
			$('#class-info').toggle();
		});
		$('#student_to_class').click(function(){
			$('#student_to_class-info').toggle();
		});
		$('#faculty_to_class').click(function(){
			$('#faculty_to_class-info').toggle();
		});
		$('#student').click(function(){
			$('#student-info').toggle();
		});
	});
	</script>
	<div id='container'>
	<SMALL><b>The following Blackbaud exports are supported.</b> Click on each for more details.</small><br>
	<table>
		<tr id='class'><td width='350px'>K-12 Class ID with Course Name.CSV</td><td style='color:#999;'><small>Updated ".$prefarray['class_update']."</td></tr>
		<tr id='class-info' style='display:none;color:#888;'><td colspan='2'>&nbsp;This file lists all classes and class names with the exact headers:<br>&nbsp; || Class ID | Course name ||<br></td></tr>
		<tr id='student_to_class'><td>K-12 Class ID with Student ID.CSV</td><td style='color:#999;'><small>Updated ".$prefarray['student_to_class_update']."</td></tr>
		<tr id='student_to_class-info' style='display:none;color:#888;'><td colspan='2'>&nbsp;This file relates student IDs to class IDs with the exact headers:<br>&nbsp; || Class ID | Student ID ||<br></td></tr>
		<tr id='faculty_to_class'><td>K-12 Faculty ID with Class ID.CSV</td><td style='color:#999;'><small>Updated ".$prefarray['faculty_to_class_update']."</td></tr>
		<tr id='faculty_to_class-info' style='display:none;color:#888;'><td colspan='2'>&nbsp;This file relates faculty IDs to class IDs with the exact headers:<br>&nbsp; || Record ID | Student ID ||<br></td></tr>
		<tr id='student'><td>K-12 Student Info.CSV</td><td style='color:#999;'><small>Updated ".$prefarray['student_update']."</td></tr>
		<tr id='student-info' style='display:none;color:#888;'><td colspan='2'>&nbsp;This file lists all students and associated information, with the exact headers:<br>&nbsp; || Last name | First name | Nickname | Student ID | Class of | Current Grade ||<br></td></tr>

	</table><br>

	<form  method='post' enctype='multipart/form-data'><center>

	<input type='file' name='file' id='file' /> 

	<input type='submit' name='submit' value='Upload'/></center>
	</form>


	</body>
	</html>";

// echo var_dump($csv);



}

// Swap back to original year
$_SESSION['year'] = $_SESSION['temp_year'];


?>








