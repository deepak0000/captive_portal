<?php

	$a=$_COOKIE['sid'];
	$b=$_COOKIE['name'];
	$c=$_COOKIE['company'];
	$conn= mysql_connect("localhost","root","<password>");
	mysql_select_db("details",$conn);
	$re= mysql_query("INSERT INTO ud(name,sid,company) VALUES('$b','$a','$c')");
	mysql_close($conn);
?>