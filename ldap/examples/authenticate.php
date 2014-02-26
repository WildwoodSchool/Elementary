<?php

      mysql_connect("localhost", "root", "tanner") or die(mysql_error());
      mysql_select_db("zack_main") or die(mysql_error());
//log them out
$logout=$_GET['logout'];
if ($logout=="yes"){ //destroy the session
	session_start();
	$_SESSION = array();
	session_destroy();
}

//force the browser to use ssl (STRONGLY RECOMMENDED!!!!!!!!)
//if ($_SERVER["SERVER_PORT"]!=443){ header("Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']); exit(); }

//you should look into using PECL filter or some form of filtering here for POST variables
$username=$_POST["username"]; //remove case sensitivity on the username
$password=$_POST["password"];
$formage=$_POST["formage"];



if ($_POST["oldform"]){ //prevent null bind

	if ($username!=NULL && $password!=NULL){
		//include the class and create a connection
		include ("../adLDAP.php");
        try {
		    $adldap = new adLDAP();
        }
        catch (adLDAPException $e) {
            echo $e; exit();   
        }
		
		//authenticate the user
		if ($adldap -> authenticate($username,$password)){
		
			//establish your session and redirect
			$sql = "select * from teacher_accounts where username = '$username'";
			$result = mysql_query($sql) or die (mysql_error());
			if(mysql_num_rows($result) > 0) {
		
			
			session_start();
			$_SESSION['teacher'] = $username;
			$redir="Location: http://".$_SERVER['HTTP_HOST']."/attendance/attendance.php";
			header($redir);
			exit;
			}
		}
		
	
		
	}
	$failed=1;
}


?>

<html>
<head>
<title>adLDAP example</title>
</head>

<body>

This area is restricted.<br>
Please login to continue.<br>

<form method='post' action='<?php echo $_SERVER["PHP_SELF"]; ?>'>
<input type='hidden' name='oldform' value='1'>

Username: <input type='text' name='username' value='<?php echo ($username); ?>'><br>
Password: <input type='password' name='password'><br>
<br>

<input type='submit' name='submit' value='Submit'><br>
<?php if ($failed){ echo ("<br>Login Failed!<br><br>\n"); } ?>
</form>

<?php if ($logout=="yes") { echo ("<br>You have successfully logged out."); } ?>


</body>

</html>

