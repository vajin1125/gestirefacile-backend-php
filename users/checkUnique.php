<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


if(isset($_GET['email'])) {
	
	$email = $_GET['email'];
	$sql = "SELECT * FROM users where email='{$email}'";
	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
		}
		$con->close();
		echo json_encode($newArr);

	}
}

if(isset($_GET['username'])) {
	
	$username = $_GET['username'];
	$sql = "SELECT * FROM users where username='{$username}'";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
		}
		$con->close();
		echo json_encode($newArr);

	}
}

?>