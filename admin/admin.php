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


// If submitted, get POST data
if(isset($_POST['submit']) && ($_GET['page'] == "lock")){

	$nicelockdate = $_POST['lockdate'];
	$lockdate = date("Y-m-d",strtotime($nicelockdate));
	
	$today = date("Y-m-d");
	if($today < $lockdate){
		$override = 0;
	}
	else{
		$override = $_POST['override'];
	}
	
	mysql_query("UPDATE elem_site_prefs SET pref_value = '$lockdate' WHERE pref_name = 'setting_lock_date'") or die (mysql_error());
	mysql_query("UPDATE elem_site_prefs SET pref_value = '$override' WHERE pref_name = 'setting_lock_override'") or die (mysql_error());
}		

if(isset($_POST['submit']) && ($_GET['page'] == "subjects")){
	$new_social_notes_name = mysql_real_escape_string($_POST['social_notes_name']);
	$new_social_notes_abbr = mysql_real_escape_string($_POST['social_notes_abbr']);
	$socsql = "UPDATE elem_site_prefs SET pref_value = '$new_social_notes_name' WHERE pref_name = 'social_notes_name'";
	mysql_query($socsql) or die (mysql_error());
	$socsql = "UPDATE elem_site_prefs SET pref_value = '$new_social_notes_abbr' WHERE pref_name = 'social_notes_abbr'";
	mysql_query($socsql) or die (mysql_error());



	$new_order = mysql_real_escape_string($_POST['table-return']);
	$new_order = explode(",",$new_order);
	foreach($new_order as $key => $subjectid){
		$new_subname = mysql_real_escape_string($_POST["subj_name".$subjectid]);
		$new_subabbr = mysql_real_escape_string($_POST["subj_abbr".$subjectid]);
		$new_sort = $key + 1;
		$subjsql = "UPDATE elem_subjects SET subject_name = '$new_subname', subject_abbr = '$new_subabbr', subject_sort = '$new_sort' WHERE subjectid = '$subjectid'";
//		echo 	$subjsql."<br>";
		mysql_query($subjsql) or die (mysql_error());
	}
}

if(isset($_POST['submit']) && ($_GET['page'] == "assessments")){
	$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '1' ORDER BY assess_sort DESC";
	$assessresult = mysql_query($assesssql) or die (mysql_error());
	while($assessrow = mysql_fetch_array($assessresult)){
		$id = $assessrow['assessid'];		
		$new_deft = mysql_real_escape_string($_POST["assess_deft".$id]);
		$new_name = mysql_real_escape_string($_POST["assess_name".$id]);
		$new_abbr = mysql_real_escape_string($_POST["assess_abbr".$id]);
		$new_descrip = mysql_real_escape_string($_POST["assess_descrip".$id]);
		$newsql = "UPDATE elem_assessment_values SET assess_name = '$new_name', assess_abbr = '$new_abbr', assess_descrip = '$new_descrip' WHERE assessid = '$id'";
		mysql_query($newsql) or die (mysql_error());
	}
	$new_name = mysql_real_escape_string($_POST["unassess_name"]);
	$new_descrip = mysql_real_escape_string($_POST["unassess_descrip"]);
	$newsql = "UPDATE elem_assessment_values SET assess_name = '$new_name', assess_descrip = '$new_descrip' WHERE set_id = '0'";
	mysql_query($newsql) or die (mysql_error());
	$colorsql = "UPDATE elem_site_prefs SET pref_value = '".$_POST['rowcolor']."' WHERE pref_name = 'spring_color'";
	mysql_query($colorsql) or die(mysql_error()); 
	
}


// Reset $_SESSION arrays
if(isset($_POST['submit'])){
	$prefarray = null;
	$_SESSION['site_prefs'] = null;

	$subj_name_array = null;
	$_SESSION['subject_names'] = null;

	$subj_abbr_array = null;
	$_SESSION['subject_abbrs'] = null;
	

	include ("../includes/arrays.php");
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
		$(document).ready(function() {
		
	  	 	$("#spring_color").css('background-color', ($("#spectrum").val()));
		  	 
			// Initialise the table
			$("#table-1").tableDnD({
				onDragClass: "myDragClass",
			});
			
			$("#spectrum").spectrum({
			    preferredFormat: "hex",
			    showPalette: true, palette: ['#a1dadc', 'rgb(169, 220, 161)'], 
			    showInitial: true,
			    change: function(){
			    	$("#spring_color").css('background-color', ($("#spectrum").val()))
			    	
			    	}
		   	 });
	   	
	});
	</script>
	
	<script type="text/javascript">
		function newRowOrder(){
			var trs = document.getElementsByTagName('tr');
			var newOrder = trs[1].id;
			for (var i=2; i<trs.length; i++) {
				 newOrder += "," + trs[i].id;
			}
			document.getElementById('table-return').value = newOrder;
		}

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

echo "<h3>Administrative Options</h3>";
$subnav = "<ul id='subnav'>
<li><a href='?page=assessments'>Assessment Names</a></li>
<li><a href='?page=subjects'>Subject Names</a></li>
<li><a href='?page=lock'>Lock Template Pages</a></li>
<li><a href='?page=database'>Database</a></li>
</ul>";
if(isset($_GET['page'])){
	$subnav = str_replace("<li><a href='?page=".$_GET['page'],"<li class='current'><a href='?page=".$_GET['page'],$subnav);
}
echo $subnav;
//echo var_dump($prefarray);



// Lock Page
if($_GET['page'] == "lock"){

	$today = date("Y-m-d");
	if(($today < $prefarray['setting_lock_date'])){
		$showover = "display:none";
	}
	else{
		$calcolor = "color:red";
	}

	$nicelockdate = date("m/d/Y",strtotime($prefarray['setting_lock_date']));
	
	if($prefarray['setting_lock_override'] == 1){
		$yescheck = "checked='checked'";
	}
	
	else{
		$nocheck = "checked='checked'";
	}


	echo "<form name='prefs' method='POST'>
	<div style='width:95%;height:300px;margin-left:15px'>
	<div style='float:left;width:420px;font-size:150%'>
	<table style='padding:8px'>
	<tr><td colspan='2'>Automatically lock Template Setup pages on: <input type='text' size='8' value='".$nicelockdate."' id='datepicker' style='font-size:13px;$calcolor;' name='lockdate'></td></tr>
	<tr><td height='40px' align='center' valign='bottom'><div style='".$showover."'><font color='red' ><b>Pages are currently locked</b></font></td></tr>
	<tr><td height='30px'><div style='".$showover."'>Override and allow all users to access locked pages?</div></td><td><div style='".$showover."'><input type='radio' value='1' name='override' ".$yescheck." > Yes<br><input type='radio' value='0' name='override' ".$nocheck." > No</div></td><tr>
	</table></div>
	<div style='float:right;width:420px;height:300px'><br><img src='/elementary/imgs/info.png' width='35px' height='35px' style='float:left'><i>Select the date to automatically lock the pages under the Template Setup menu. When the pages are locked, teachers will not be
	able to access or change any course descriptions, headings or skills. This can be overridden by allowing all users to access locked pages or changing the date. Overriding will NOT automatically re-lock. Administrators will always have access to all pages.<br>
	<b>Make sure to save your changes when done.</b></i><br>
	<br><br><input name='submit' type='submit' style='font-size:100%' value='Save Settings'></form>";
}

// Subject names and order Page
if($_GET['page'] == "subjects"){

	echo "<form name='prefs' method='POST' onsubmit='newRowOrder();'>
	<div style='width:95%;height:300px;margin-left:15px'>
	<div style='float:left;height:300px;width:400px;font-size:150%'><table id='table-1' class='table' style='font-size:105%;margin-left:10px' >
	<tr><th width='270px' height='30px'>Subject Full Name</th><th>Abbreviation</th></tr>
	<tr class='nodrag'><td style='padding:0 10px 2px 10px;'><input size='28' style='font-size:18px;border: transparent;background: transparent;outline: none;' name='social_notes_name' value='".stripslashes($prefarray['social_notes_name'])."'></td>
		<td><input style='font-size:18px;border:transparent;background:transparent;outline:none;' size='8' name='social_notes_abbr' value='".stripslashes($prefarray['social_notes_abbr'])."'></td></tr><tbody>";

	$subjsql = "SELECT * FROM elem_subjects ORDER BY subject_sort ASC";
	$subjresult = mysql_query($subjsql) or die (mysql_error());
	while($subjrow = mysql_fetch_array($subjresult)){
		echo "<tr id='".$subjrow['subjectid']."'><td style='padding:0 10px 2px 10px;'><input size='18' style='font-size:18px;border: transparent;background: transparent;outline: none;' name='subj_name".$subjrow['subjectid']."' value='".stripslashes($subjrow['subject_name'])."'></td>
		<td><input style='font-size:18px;border:transparent;background:transparent;outline:none;' size='8' name='subj_abbr".$subjrow['subjectid']."' value='".stripslashes($subjrow['subject_abbr'])."'></td></tr>";  	
	}
	echo "</table></div>
	<div style='float:right;width:350px;height:300px'><br><img src='/elementary/imgs/info.png' width='35px' height='35px' style='float:left'><i>To edit subject names, simply click in the box and type. When reports are printed, the subjects will print in this order. To rearrange the subjects, grab the edges and drag into the order you want. (Note: The subject abbreviations are only used for menus in this system and will not appear on any printed reports).<br>
	<b>Make sure to save your changes when done.</b></i><br><br><input type='hidden' id='table-return' name='table-return'><input name='submit' type='submit' style='font-size:100%' value='Save Subjects'></form>";
}

// Assessments Page
if($_GET['page'] == "assessments"){
	$assesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '1' ORDER BY assess_sort DESC";
	$assessresult = mysql_query($assesssql) or die (mysql_error());
	echo "<br><img src='/elementary/imgs/info.png' width='35px' height='35px' style='float:left'><i>To edit any Assessment name, abbreviation, or description, simply click in the box and type. This information will be printed <br>on the first page of the progress reports. Note: Using \"#fname\" will print each individual student's name when downloaded.
	<br><b>Make sure to save your changes when done.</b></i><br>";
	
	echo "<form name='prefs' method='POST'><br><table id='table' class='table' style='font-size:120%;margin-left:10px' width='95%'><tr><th width='20%'>Assessment Name</th><th width='15%'>Abbreviation</th><th width='55%'>Description</th></tr>";
	while($assessrow = mysql_fetch_array($assessresult)){
		echo "<tr><td><input style='font-size:14px;border: transparent;background:transparent;outline: none;' size='24' name='assess_name".$assessrow['assessid']."' value='".stripslashes($assessrow['assess_name'])."'></td>
		<td align='center'><input style='font-size:15px;background:transparent;border: transparent;outline: none;' size='2' name='assess_abbr".$assessrow['assessid']."' value='".stripslashes($assessrow['assess_abbr'])."'></td>
		<td><textarea rows='2' style='font-family:\"lucida sans\",sans-serif;font-size:14px;background-color:transparent;border:transparent;width:100%;resize:none' name='assess_descrip".$assessrow['assessid']."'>".stripslashes($assessrow['assess_descrip'])."</textarea></td></tr>";  	
	}
		$unassesssql = "SELECT * FROM elem_assessment_values WHERE set_id = '0'";
		$unassessresult = mysql_query($unassesssql) or die (mysql_error());
		$unassessrow = mysql_fetch_array($unassessresult);
		echo "<tr id='spring_color' ><td><input style='font-size:14px;border: transparent;background:transparent;outline: none;' size='24' name='unassess_name' value='".stripslashes($unassessrow['assess_name'])."'></td>
		<td></td>
		<td><br><textarea rows='2' style='font-family:\"lucida sans\",sans-serif;font-size:14px;background-color:transparent;border:transparent;width:100%;resize:none' name='unassess_descrip'>".stripslashes($unassessrow['assess_descrip'])."</textarea></td></tr>
		</table><br><input id='spectrum' type='color' name='rowcolor' value='".$prefarray['spring_color']."' > &nbsp;".stripslashes($unassessrow['assess_name'])." Row Color<br><input name='submit' type='submit' style='margin-right:30px;float:right;font-size:100%' value='Save Assessments'></form>";
	
}

if($_GET['page'] == "database"){

	$filename = "http://connections.wildwood.org/elementary/includes/yearfile.txt";

	// Read old file
// 	$read = fopen("$filename", "r");
// 	echo var_dump($_SESSION['yeararray']);
// 	$yearfile = fopen($filename, 'w') or die("can't open file");

// 	fwrite($yearfile, $yearline);
	

}

?>