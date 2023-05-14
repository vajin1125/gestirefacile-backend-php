<?php
// database connection will be here
require '../config/security.php';
// require '../config/database.php';


$oid_user = $currentUser['oid'];

if(isset($_GET['oid'])) {
	$oid = $_GET['oid'];
	$sql = "SELECT * FROM warehouse_movements WHERE oid='{$oid}'";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		$oid_resource_type = null;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$oid_warehouse = $db_field['oid_warehouse'];
			$oid_event = $db_field['oid_event'];	
			$oid_user = $db_field['oid_user_operation'];
			$oid_resource = $db_field['oid_resource'];
			$oid_type_movement = $db_field['oid_type_movement'];
			$newArr[] = $db_field;
		}
		
		
		$sqlw = "SELECT * FROM warehouse WHERE oid='{$oid_warehouse}'";
		$warehouse = null;
		if($resultW = mysqli_query($con,$sqlw)) {
			if ($db_field_w = mysqli_fetch_assoc($resultW))  {
				$warehouse = $db_field_w;
			}
			else {
				http_response_code(500);
			}
		}
		$newArr[0]['warehouse'] = $warehouse;
		
		$sqle = "SELECT * FROM events WHERE oid='{$oid_event}'";
		$event = null;
		if($resultE = mysqli_query($con,$sqle)) {
			if ($db_field_e = mysqli_fetch_assoc($resultE))  {
				$event = $db_field_e;
			}
			else {
				http_response_code(500);
			}
		}
		$newArr[0]['event'] = $event;
		
		$sqlu = "SELECT * FROM users WHERE oid='{$oid_user}'";
		$user = null;
		if($resultU = mysqli_query($con,$sqlu)) {
			if ($db_field_u = mysqli_fetch_assoc($resultU))  {
				$user = $db_field_u;
			}
			else {
				http_response_code(500);
			}
		}
		$newArr[0]['user'] = $user;
	
		$sqlr = "SELECT * FROM resources WHERE oid='{$oid_resource}'";
		$resource = null;
		if($resultR = mysqli_query($con,$sqlr)) {
			if ($db_field_r = mysqli_fetch_assoc($resultR))  {
				$resource = $db_field_r;
			}
			else {
				http_response_code(500);
			}
		}
		$newArr[0]['resource'] = $resource;
		
		
		$sqlt = "SELECT * FROM movement_types WHERE oid='{$oid_type_movement}'";
		$movementType = null;
		if($resultT = mysqli_query($con,$sqlt)) {
			if ($db_field_t = mysqli_fetch_assoc($resultT))  {
				$movementType = $db_field_t;
			}
			else {
				http_response_code(500);
			}
		}
		$newArr[0]['type'] = $movementType;
			

		$con->close();
		echo json_encode($newArr);

	}
}
else {
	$sql = "SELECT * FROM warehouse_movements WHERE oid_warehouse IN (SELECT oid FROM warehouse WHERE oid_busINess in (SELECT oid_business FROM role_user_assoc WHERE oid_user= '{$oid_user}')) ORDER BY oid DESC";

	if($result = mysqli_query($con, $sql))
	{
		$newArr = array();
		$idx = 0;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
			$oid_warehouse = $db_field['oid_warehouse'];
			$oid_event = $db_field['oid_event'];	
			$oid_user = $db_field['oid_user_operation'];
			$oid_resource = $db_field['oid_resource'];
			$oid_type_movement = $db_field['oid_type_movement'];
			
			
			$sqlw = "SELECT * FROM warehouse WHERE oid='{$oid_warehouse}'";
			$warehouse = null;
			if($resultW = mysqli_query($con,$sqlw)) {
				if ($db_field_w = mysqli_fetch_assoc($resultW))  {
					$warehouse = $db_field_w;
				}
				else {
					http_response_code(500);
				}
			}
			$newArr[$idx]['warehouse'] = $warehouse;
			
			
			$sqle = "SELECT * FROM events WHERE oid='{$oid_event}'";
			$event = null;
			if($resultE = mysqli_query($con,$sqle)) {
				if ($db_field_e = mysqli_fetch_assoc($resultE))  {
					$event = $db_field_e;
				}
				else {
					http_response_code(500);
				}
			}
			$newArr[$idx]['event'] = $event;
			
			$sqlu = "SELECT * FROM users WHERE oid='{$oid_user}'";
			$user = null;
			if($resultU = mysqli_query($con,$sqlu)) {
				if ($db_field_u = mysqli_fetch_assoc($resultU))  {
					$user = $db_field_u;
				}
				else {
					http_response_code(500);
				}
			}
			$newArr[$idx]['user'] = $user;
			
			
			$sqlr = "SELECT * FROM resources WHERE oid='{$oid_resource}'";
			$resource = null;
			if($resultR = mysqli_query($con,$sqlr)) {
				if ($db_field_r = mysqli_fetch_assoc($resultR))  {
					$resource = $db_field_r;
				}
				else {
					http_response_code(500);
				}
			}
			$newArr[$idx]['resource'] = $resource;
			
			
			$sqlt = "SELECT * FROM movement_types WHERE oid='{$oid_type_movement}'";
			$movementType = null;
			if($resultT = mysqli_query($con,$sqlt)) {
				if ($db_field_t = mysqli_fetch_assoc($resultT))  {
					$movementType = $db_field_t;
				}
				else {
					http_response_code(500);
				}
			}
			$newArr[$idx]['type'] = $movementType;
			
			
			
			$idx = $idx + 1;
		}
		$con->close();
		echo json_encode($newArr);

	}
}


?>