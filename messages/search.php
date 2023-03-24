<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';
require '../config/superuser.php';

	$sql = "SELECT * FROM messages order by oid desc";

	if($result = mysqli_query($con,$sql))
	{
		$idx = 0;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
			if ($db_field['oid_user_sent']) {
				$oidUserSent = $db_field['oid_user_sent'];
				$sqlu = "SELECT * FROM users where oid='{$oidUserSent}'";
				if($resultU = mysqli_query($con,$sqlu)) {
					if ($db_field_u = mysqli_fetch_assoc($resultU))  {
						$user = $db_field_u;
						$newArr[$idx]['user_sent'] = $user;
					}
					else {
						http_response_code(500);
					}
				}
			}
			if ($db_field['oid_business']) {
				$oidBusiness= $db_field['oid_business'];
				$sqlb = "SELECT * FROM business where oid='{$oidBusiness}'";
				if($resultB = mysqli_query($con,$sqlb)) {
					if ($db_field_b = mysqli_fetch_assoc($resultB))  {
						$business = $db_field_b;
						$newArr[$idx]['business'] = $business;
					}
					else {
						http_response_code(500);
					}
				}
			}
			if ($db_field['oid_role']) {
				$oidRole= $db_field['oid_role'];
				$sqlr = "SELECT * FROM roles where oid='{$oidRole}'";
				if($resultR = mysqli_query($con,$sqlr)) {
					if ($db_field_r = mysqli_fetch_assoc($resultR))  {
						$role = $db_field_r;
						$newArr[$idx]['role'] = $role;
					}
					else {
						http_response_code(500);
					}
				}
			}
			
			
			$idx  = $idx  + 1;
		}
		
		
		
		$con->close();
		
		echo json_encode($newArr);
	}
	else 
	{
		echo json_encode($newArr);
	}


?>