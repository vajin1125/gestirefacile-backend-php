<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

$oid_user = $currentUser['oid'];

if(isset($_GET['oid'])) {
	$oid = $_GET['oid'];
	$sql = "SELECT * FROM payment_types where oid='{$oid}'";

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
else {
	$sql = "SELECT * FROM payment_types ";

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