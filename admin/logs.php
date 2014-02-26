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

// Failed attempt at using Zend auth. QQ
// 	$auth = Zend_Auth::getInstance();
// 	$username = str_replace("WILDWOOD\\", "",$auth->getIdentity());
//     echo $username;



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


echo "<form name='logform' method='POST'>
<h3>Event Logs</h3>
Choosing \"Clear Database\" will save the current logs to the server as a CSV and clear the log database <input type='submit' style='float:right' name='clear' value='Clear Database'>
<br><br>";
// echo "<input type='submit' name='csv' value='Download CSV'>";


if(isset($_POST['clear'])){
	$filename = "logs/elementary_log_".date('m-d-Y H-i-s').".csv";
	$csv = fopen($filename, 'w') or die("can't open file");
	$csvline = 	"\"URL\",\"USER ID\",\"DATA\",\"TIMESTAMP\"\n";
	fwrite($csv, $csvline);

	$logsql = "SELECT * FROM elem_logs";
	$logresult = mysql_query($logsql) or die (mysql_error());
	while($logrow = mysql_fetch_array($logresult)){
		$data = str_replace("\"","'",$logrow['postdata']);
		$csvline = 	"\"".$logrow['pageurl']."\",\"".$logrow['userid']."\",\"".$data."\",\"".$logrow['timestamp']."\"\n";
		fwrite($csv, $csvline);	
	}
	$logsql = "TRUNCATE TABLE elem_logs";
	$logresult = mysql_query($logsql) or die (mysql_error());
	
	echo "<center><b>The database has been cleared.</b></center><br>
	<br>The saved log file is accessable at http://connections.wildwood.org/elementary/admin/".$filename."
	<br><a href='http://connections.wildwood.org/elementary/admin/".$filename."' style='text-decoration:underline;font-weight:bold;'>Click Here</a> to download<br><br>";

}

$logsql = "SELECT * FROM elem_logs ORDER BY logid DESC";
$logresult = mysql_query($logsql) or die (mysql_error());
$lognum = mysql_num_rows($logresult);
echo "Log Entries: $lognum<br><br><table class='table' style='width:100%'>
<tr><td>URL</td><td>User ID</td><td>Data</td><td>Timestamp</td></tr>";

while($logrow = mysql_fetch_array($logresult)){
	echo "<tr>
	<td style='border:1px solid #999999; vertical-align:top'><a href='".$logrow['pageurl']."'>".$logrow['pageurl']."</a></td>
	<td style='border:1px solid #999999; vertical-align:top'>".$logrow['userid']."</td>
	<td style='border:1px solid #999999; vertical-align:top'><div style='width:500px;height:5em;overflow:auto;'>".$logrow['postdata']."</div></td>
	<td style='border:1px solid #999999; vertical-align:top'>".$logrow['timestamp']."</td></tr>";
}
echo "</table>";


?>