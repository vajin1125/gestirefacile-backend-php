<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

$oid_user = $currentUser['oid'];

if(isset($_GET['oid'])) {
	$oid = $_GET['oid'];
	$sql = "SELECT * FROM warehouse where oid='{$oid}' and oid_user={$oid_user}";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		$oid_business = null;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$oid_business = $db_field['oid_business'];
			$newArr[] = $db_field;
		}
		
		$sqlr = "select * from business where oid={$oid_business}";
		$business = null;
		if($resultR = mysqli_query($con,$sqlr)) {
			if ($db_field_r = mysqli_fetch_assoc($resultR))  {
				$business = $db_field_r;
			}
			else {
				http_response_code(500);
			}
		}
		

		$newArr[0]['business'] = $business;
			
		//unset($newArr[0]['business']);
		$con->close();
		echo json_encode($newArr);

	}
}
else {
	$sql = "SELECT * FROM warehouse where oid_user={$oid_user}  order by oid desc";

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