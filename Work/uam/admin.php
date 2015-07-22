<html>
	<style>
		body{
					background-color: light-gray;
		}
		label{
					font: 23px Open Sans;
					color: #100000;
					text-shadow:#000 0px 1px 5px;
					margin-bottom: 30px;
		}	
		h1{
				    font: 23px Open Sans;
					color: #100000;
					text-shadow: #000 0px 1px 5px;
					text-align:center;
					margin-bottom: 30px;
		}			
		input[type="submit"] {
  		width: 7%;
  		padding: 10px;
  		border-radius: 5px;
  		outline: none;
  		border: none;
  		background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#FF0000 ), to(#680000 ));
  		background-image: -webkit-linear-gradient(#FF0000  0%, #680000  100%);
  		background-image: -moz-linear-gradient(#FF0000  0%, #680000  100%);
  		background-image: -o-linear-gradient(#FF0000  0%, #680000  100%);
  		background-image: linear-gradient(#FF0000  0%, #680000  100%);
  		font: 14px Oswald;
  		color: #FFF;
  		text-transform: uppercase;
  		text-shadow: #000 0px 1px 5px;
  		border: 1px solid #000;
  		opacity: 0.7;
  		-webkit-box-shadow: 0 8px 6px -6px rgba(0, 0, 0, 0.7);
  		-moz-box-shadow: 0 8px 6px -6px rgba(0, 0, 0, 0.7);
  		box-shadow: 0 8px 6px -6px rgba(0, 0, 0, 0.7);
    	border-top: 1px solid rgba(255, 255, 255, 0.8) !important;
 		 -webkit-box-reflect: below 0px -webkit-gradient(linear, left top, left bottom, from(transparent), color-stop(50%, transparent), to(rgba(255, 255, 255, 0.2)));
		}

		button[type="submit"] {
  		width: 80%;
  		padding: 10px;
  		border-radius: 5px;
  		outline: none;
  		border: none;
  		background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#FF0000 ), to(#680000 ));
  		background-image: -webkit-linear-gradient(#FF0000  0%, #680000  100%);
  		background-image: -moz-linear-gradient(#FF0000  0%, #680000  100%);
  		background-image: -o-linear-gradient(#FF0000  0%, #680000  100%);
  		background-image: linear-gradient(#FF0000  0%, #680000  100%);
  		font: 14px Oswald;
  		color: #FFF;
  		text-transform: uppercase;
  		text-shadow: #000 0px 1px 5px;
  		border: 1px solid #000;
  		opacity: 0.7;
  		-webkit-box-shadow: 0 8px 6px -6px rgba(0, 0, 0, 0.7);
  		-moz-box-shadow: 0 8px 6px -6px rgba(0, 0, 0, 0.7);
  		box-shadow: 0 8px 6px -6px rgba(0, 0, 0, 0.7);
    	border-top: 1px solid rgba(255, 255, 255, 0.8) !important;
 		 -webkit-box-reflect: below 0px -webkit-gradient(linear, left top, left bottom, from(transparent), color-stop(50%, transparent), to(rgba(255, 255, 255, 0.2)));
		}
		
		button:focus {
  		box-shadow: inset 4px 6px 10px -4px rgba(0, 0, 0, 0.7), 0 1px 1px -1px rgba(255, 255, 2, 0.3);
  		background: rgba(0, 0, 0, 0);
  		-webkit-transition: 1s ease;
  		-moz-transition: 1s ease;
  		-o-transition: 1s ease;
  		-ms-transition: 1s ease;
   		transition: 1s ease;
		}

		button[type="submit"]:hover {
  		opacity: 1;
  		cursor: pointer;
		input:focus {
  		box-shadow: inset 4px 6px 10px -4px rgba(0, 0, 0, 0.7), 0 1px 1px -1px rgba(255, 255, 2, 0.3);
  		background: rgba(0, 0, 0, 0);
  		-webkit-transition: 1s ease;
  		-moz-transition: 1s ease;
  		-o-transition: 1s ease;
  		-ms-transition: 1s ease;
   		transition: 1s ease;
		}

		button[type="submit"]:hover {
  		opacity: 1;
  		cursor: pointer;
  		
	</style>
</html>

<?php
session_start();

$username = '<username>';
$password = '<password>';

$random1 = 'secret_key1';
$random2 = 'secret_key2';

$hash = md5($random1.$password.$random2); 

$self = $_SERVER['REQUEST_URI'];


if(isset($_GET['logout']))
{
	unset($_SESSION['login']);
}


if (isset($_SESSION['login']) && $_SESSION['login'] == $hash) {

	?>
		<br><br>
		<a href="?logout=true"><img src='logout.png' style='width:60px;height:60px;' align='right'   ''></a> 
		<h1>Users currently connected  </h1><br>
	<?php
	echo '<pre>';
	$conn= mysql_connect("localhost","root","root");
	mysql_select_db("details",$conn);
	$v1=shell_exec("sudo chilli_query list ");
	
	//$v2=explode("\n",$v1);
	$v2=preg_split("/[ \n]/",$v1);
	$ans=array();
	foreach($v2 as $key => $value) {
		if($key % 15 ==0){
		$ind=$key+3;
		$chk=$key+2;
		if($v2[$chk]=="pass"){	
		mysql_query("UPDATE  ud SET mac = '$value'  WHERE sid ='$v2[$ind]'");
		//echo "Session Id : $v2[$ind]  MAC : $value<br>";
		 }
		}
		}
		
	echo '</pre>';
	?>
		

	<?php
	$var=$_POST['mad'];
	
	if(isset($_POST['discb'])) {
	$d=$_POST['discb'];
	system("sudo chilli_query logout $d");
	echo "Disconnected"; $d=1;
	Header('Location: '.$_SERVER['PHP_SELF']);
	//unset($_POST['discb']);
		}
		
	echo "<br/>";
	echo "<br/>";

	if(!$conn) {
		echo "FAIL";
	}
	else {
	$v4=shell_exec("sudo chilli_query list ");
	$v3=preg_split("/[ \n]/",$v4);
	foreach($v3 as $key => $val) {
		$v3[$key] = mysql_real_escape_string($val); }
	$newarr= "'".implode("', '",$v3)."'";
	$ini= mysql_query("DELETE FROM ud WHERE sid NOT IN ($newarr) "); 
		$count=0;
		$re= mysql_query("SELECT * FROM ud");
		echo "<table style='width:60%' border =1 align='center'>";
		echo "<tr> <th id='name'>Name</th> <th id='cname'>Company name</th><th id='st'>Action</th></tr>";
		while($row = mysql_fetch_array($re))
		{
			$count=$count+1;
			echo "<tr><td align ='center' headers ='name'>".$row['name']."</td> <td align ='center' headers='cname'> ".$row['company']."</td>";
			echo "<form method='POST' action=".$_SERVER["PHP_SELF"].">";
			echo "<td align='center' headers='st'><button type='submit' name='discb' value=".$row["mac"].">Disconnect</button></td></tr>";
			//echo "<br/><br>";
		}	echo "</table>";
		echo "<br><br><center>Total number of connected users : ".$count."</center>";
		mysql_close($conn);
		
	}	
	
}



else if (isset($_POST['submit'])) {

	if ($_POST['username'] == $username && $_POST['password'] == $password){
	
		$_SESSION["login"] = $hash;
		header("Location: $_SERVER[PHP_SELF]");
		
	} else {
		
		display_login_form();
		echo '<p>Username or password is invalid</p>';
		
	}
}	

else { 

	display_login_form();

}


function display_login_form(){  ?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']?>" method='post'>
	<label for="username">Username<br></label>
	<input type="text" name="username" id="username"><br>
	<label for="password">Password<br></label>
	<input type="password" name="password" id="password"><br>
	<input type="submit" name="submit" value="submit">
	</form>	

<?php } ?>

