<?php

// Connect to Database
require "includes/database.php";    

// Check Login
$username=mysql_real_escape_string($_POST["username"]);
$username = str_replace("@wildwood.org", "", $username);
$password=mysql_real_escape_string($_POST["password"]);

if(isset($_POST['login']) && isset($_POST['username']) && isset($_POST['password'])) {

	if ($username!=NULL && $password!=NULL){
		//include the class and create a connection
		require ("./ldap/adLDAP.php");
		try {
			$adldap = new adLDAP();
		}
		catch (adLDAPException $e) {
			echo $e; exit();   
		}
		
		//authenticate the user
		if ($adldap -> authenticate($username,$password)){
			//establish your session and redirect
			$sql = "SELECT * FROM teacher_accounts WHERE username = '$username'";
			$result = mysql_query($sql) or die (mysql_error());
			$row = mysql_fetch_assoc($result);
			if((mysql_num_rows($result) > 0) && ((strpos($row['flags'], "d")) === false)) {
				session_set_cookie_params(3600 * 24 * 7);
				session_start();
				$userarray = array(
					"name"		=> $username,
					"id"		=> $row['id'],
					"flags"		=> $row['flags'],
					"fname"		=> $row['fname'],
					"lname"		=> $row['lname'],
					"campus"	=> $row['campus']
					);
				$_SESSION['user'] = $userarray;	
				
				$postdata = "Login Successful: ".$userarray['fname']." ".$userarray['lname'];
				$logsql = "INSERT INTO elem_logs (pageurl, userid, timestamp, postdata, visitorip) VALUES ('$pageurl', '$userarray[id]', '$timestamp', '$postdata', '$visitorip')";
				mysql_query($logsql) or die (mysql_error());

				// Check that user is admin
				if((strpos($userarray['flags'], "r")) === false){    
					header ("Location: ./index.php");
				}
				else{
 					header("Location: ./admin/reader.php");
				}
			}
			
		}
		else{
			$error = 1;
		}
	}
}
?>

<html>
<head>
    <title>Wildwood Teacher Login</title>
    <style type="text/css">
        body {
            background-image:url('/images/login.png');
            background-repeat:no-repeat;
            background-attachment:fixed;
            background-position:center top;

            font-family: arial, impact, sans-serif;
            font-size: 20px;

        }
        .align {
            margin-top: 250px;
            width: 520px;
            margin-left:auto;
            margin-right:auto;

        }

        hr {
            color: #CCCCCC;

        }

    </style>
</head>
<body><center>
    <div class="align">

<?php

if($error == 1)	{
	echo "<h6><font color='red'><b>Incorrect Username or Password</b></font></h6>";
}


echo "<form method='POST'><input name='username' size='20' type='text' value='".$username."' style='font-size:100%' placeholder='Username'><br><br><input name='password' size='20' type='password' style='font-size:100%'  placeholder='Password'><br><bR><input type='submit' name='login' class='buttons' value='Login' style='font-size:100%;float:right;margin-right:100px'>";

?>    
    
</div>

</center>
</form>
</body>
</html>