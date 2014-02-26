<?php

// Require Authenticated User
// Start Sessions, checks login and adds logout action
// Gets $user[info]; 
require ("../includes/usercheck.php");

// Connect to elementary DB
require ("../includes/database.php");

// Get Array Values
include ("../includes/arrays.php");


// Check that user is admin
if((strpos($user[flags], "a")) === false){    
		header ("Location: /elementary/index.php");
}




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

echo "<h3>Manage Users</h3>";

//Failed attempt at adLDAP directory listing
require ("../ldap/adLDAP.php");
try {
	$adldap = new adLDAP();
}
catch (adLDAPException $e) {
	echo $e; exit();   
}
// $result = $adldap -> user_info("asonderleiter");
// $result = $adldap->group_info('Wildwood Community', array('member'));
// $folders = $adldap->user_info("asonderleiter", array('*'));
$folders = $adldap->all_distribution_groups();

//echo var_dump($folders);


foreach($folders as $key => $value){
	echo "<b>".$key."</b> => ".$value;
	if(is_array($value)){
		echo " { <br>";
		foreach($value as $key => $innervalue){
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color='blue'><b>".$key."</b></font> => ".$innervalue;
				if(is_array($innervalue)){
					echo " { <br>";
					foreach($innervalue as $key => $innervalue2){
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color='darkblue'><b>".$key."</b></font> => ".$innervalue2."<br>";	
					}
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}";
				}	
		echo "<br>";
		}
		echo "}";

	}
	echo "<br><br>";
}



// $var1 = $folders[100];
// echo var_dump($var1);

// $members = $var1['member'];
// foreach($members as $value){
// 	$value = explode(",",$value);
// 	
// 	foreach($value as $name){
// 	
// 		echo $name."<br>";
// 	}
// 	echo "<br><bR>";
// }

if($_GET['view'] != "edit"){
	echo "<table><tr><th>First Name</th><th>Last Name</th><th>AD Username</th><th>Employee ID</th></tr>";
	$usersql = "SELECT * FROM teacher_accounts ORDER BY lname ASC";
	$userresult = mysql_query($usersql) or die (mysql_error());
	while ($userrow = mysql_fetch_array($userresult)){
		$id = $userrow['id'];
		$autoid = $userrow['autoid'];
		$role = $userrow['flags'];
		if($id != $user['id']){
			echo "<tr>
			<td>".$userrow['fname']."</td>
			<td>".$userrow['lname']."</td>
			<td>".$userrow['username']."</td>
			<td>".$id."</td>
			</tr>";
		
		}	
	}
	echo "</table>";
}


if($_GET['view'] == "edit"){
	echo "<table><tr><th>First Name</th><th>Last Name</th><th>AD Username</th><th>Employee ID</th><th>Role</th><th>Delete</th></tr>";
	$usersql = "SELECT * FROM teacher_accounts ORDER BY lname ASC";
	$userresult = mysql_query($usersql) or die (mysql_error());
	while ($userrow = mysql_fetch_array($userresult)){
		$id = $userrow['id'];
		$autoid = $userrow['autoid'];
		$role = $userrow['flags'];
		if($id != $user['id']){
			echo "<tr><td><input name='fname[$autoid]' size='15' value='".$userrow['fname']."'></td>
			<td><input name='lname[$autoid]' size='15' value='".$userrow['lname']."'></td>
			<td><input name='id[$autoid]' size='15' value='".$userrow['username']."'></td>
			<td><input name='id[$autoid]' size='10' value='".$id."'></td><td><select name='flags[$id]' style='width:70px'>";
				foreach($roles_array as $key => $value){
					$option =  "<option value='$key'>$value</option>";
					$option = str_replace("value='$role'", "value='$role' selected='selected'", $option);
					echo $option;
				}
			echo "</select></td>
			<td align='middle'><input type='checkbox' name='delete[] value='$autoid'></td></tr>";
		
		}	
	}
	echo "</table>";
}
?>








