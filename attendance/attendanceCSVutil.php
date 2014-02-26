<?php

// Connect to elementary DB
require ("../includes/database.php");

// $source = file_get_contents('elem.CSV');
// echo $source;
$file = 'elem.CSV';

if (is_file($file) === true)
echo "Opening File . . . <br>";
{
	$file = fopen($file, 'r');

	if (is_resource($file) === true)
	{
		$data = array();
		echo "Getting data . . .<br>Building array . . . ";
		
		while (feof($file) === false)
		{
			echo ". ";
			$data[] = fgets($file);
		}
	}
}
	echo "<br>";	
foreach($data as $line){

	$i = 0;
	$line = str_replace("\"","",$line);
	$stu = explode(",",$line);
	foreach($stu as $newline){
		$i++;
		if(is_numeric(substr($stu[$i],0,1))){
			$stuid = $stu[0];
			$date = $stu[$i];
			$date = date("Y-m-d",strtotime($date));
			$status = $stu[($i+1)];
			$insert = "INSERT INTO elem_stu_attendance (stuid, att_date, status) VALUES ('$stuid', '$date', '$status')";
			mysql_query($insert) or die (mysql_error());

			echo "<b>".$stuid."</b>: ".$date." ".$status."<br>";
		}
		$i++;
	}
}

?>