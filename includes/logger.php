<?php

function get_ip_address() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }
}

$visitorip = get_ip_address();


$pageurl = $_SERVER['REQUEST_URI'];

$postdata = "";
foreach($_POST as $info => $posted){

	// Truncate posted data
	$posted = substr(mysql_real_escape_string($posted), 0, 1250);
	
	// Add to csv
	$postdata .= $info."=".$posted.";";
}

$poststart = substr($postdata, 0, 8);

$timestamp = date("Y-m-d H:i:s");

if(empty($user)){
	if($_POST['login'] == 'Login'){
		$user['id'] = "Null";
		$postdata = "Login Attempt: ".$_POST['username']."<br>IP Address: $visitorip";
	}
}

if(($postdata != "")  && (!empty($user)) && ($poststart != "semester") && ($poststart != "selectcl")){
	$logsql = "INSERT INTO elem_logs (pageurl, userid, timestamp, postdata, visitorip) VALUES ('$pageurl', '$user[id]', '$timestamp', '$postdata', '$visitorip')";
	mysql_query($logsql) or die (mysql_error());
}

?>