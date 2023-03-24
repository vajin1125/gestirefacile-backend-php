<?php
// database connection will be here
require '../config/core.php';
require '../config/database.php';


if(isset($_GET['token']) && isset($_GET['email']) &&  isset($_GET['password'])) {
	$email = $_GET['email'];
	$token = $_GET['token'];
	$password = $_GET['password'];
	$sql = "SELECT oid FROM users where email='{$email}' and reset_token='{$token}'";
	if($result = mysqli_query($con,$sql))
	{
		if ($row = mysqli_fetch_row($result))  {
			$oid = $row[0];
			$sqlUpd = "update users set reset_token='',password='{$password}' where email='{$email}' and reset_token='{$token}'";
			if(mysqli_query($con,$sqlUpd))
			{
				//OK
			}
			else {
				http_response_code(401);
				return;
			}
		}
	}
}


?>