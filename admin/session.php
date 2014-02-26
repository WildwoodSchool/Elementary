<?php
session_start(); 

$id = session_id();

echo "<div style='font-family:\"courier\"'>
<br><font color='#0082B3'>session_start</font>();<br><br>";


echo "<font color='#0082B3'>session_id</font>() = <font color='#FF2C94'>\"" . $id . "\"</font>;<br><br>";

foreach($_SESSION as $vari => $data){
	echo  "<font color='#0082B3'>".'$_SESSION</font>[<font color="#FF2C94">\''.$vari. "'</font>] = ";
	if(is_array($data)){
		echo "<font color='#150FC3'>array</font>( <br>";
		foreach($data as $innerv => $innerd){
			echo  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>".$innerv . "</b> => ";
			if(!is_numeric($innerd)){
				echo "<font color='#FF2C94'>\"" . $innerd . "\"</font>,<br>";
			}
			else{
				echo $innerd . ",<br>";
			}
		}
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;);<br><br>";
	}
	else{
		if(is_numeric($data)){
				echo  $data . ";<br><br>";
		}
		else{
			echo "<font color='#FF2C94'>\"".$data . "\"</font>;<br><br>";
		}
	}

}

echo "<br><br></div>";
	


?>