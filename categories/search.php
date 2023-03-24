<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


$oid_user = $currentUser['oid'];

if(isset($_GET['oid'])) {
	$oid = $_GET['oid'];
	$sql = "SELECT * FROM categories where oid='{$oid}' and oid_user={$oid_user}";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		$oid_resource_type = null;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$oid_resource_type = $db_field['oid_resource_type'];
			$newArr[] = $db_field;
		}
		
		$sqlr = "select * from resource_types where oid={$oid_resource_type}";
		$resourceType = null;
		if($resultR = mysqli_query($con,$sqlr)) {
			if ($db_field_r = mysqli_fetch_assoc($resultR))  {
				$resourceType = $db_field_r;
			}
			else {
				http_response_code(500);
			}
		}
		

		$newArr[0]['resourceType'] = $resourceType;
			
		unset($newArr[0]['oid_resource_type']);
		$con->close();
		echo json_encode($newArr);

	}
}
else {
	$sql = "SELECT * FROM categories where oid_user={$oid_user} order by oid desc";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		$idx = 0;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
			$oid_resource_type = $db_field['oid_resource_type'];
			
			
			$sqlr = "select * from resource_types where oid={$oid_resource_type}";
			$resourceType = null;
			if($resultR = mysqli_query($con,$sqlr)) {
				if ($db_field_r = mysqli_fetch_assoc($resultR))  {
					$resourceType = $db_field_r;
				}
				else {
					http_response_code(500);
				}
			}
			$newArr[$idx]['resourceType'] = $resourceType;
			$idx = $idx + 1;
		}
		$con->close();
		echo json_encode($newArr);

	}
}


?>