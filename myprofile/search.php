<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


$oid_user = $currentUser['oid'];

if(isset($oid_user)) {
	$sql = "SELECT * FROM users where oid={$oid_user}";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		if ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
		}
		$sql = "SELECT oid, oid_business, oid_attribute_type, name FROM attributes where oid_user={$oid_user}";
		$attributes = array();
		$idx = 0;
		if($result = mysqli_query($con,$sql)) {
			while ($row = mysqli_fetch_row($result))  {
				$oidRoleAssoc = $row[0];
				$oidBusiness= $row[1];
				$oidType = $row[2];
				$name = $row[3];
				
				$sqlb = "SELECT * FROM business where oid={$oidBusiness}";
				
				if($resultB = mysqli_query($con,$sqlb)) {
					if ($db_field_b = mysqli_fetch_assoc($resultB))  {
						$attributes[$idx]['business'] = $db_field_b;
					}
					else {
						http_response_code(500);
					}
			    }
				
				$sqlt = "SELECT * FROM attribute_type where oid={$oidType}";
				
				if($resultT = mysqli_query($con,$sqlt)) {
					if ($db_field_t = mysqli_fetch_assoc($resultT))  {
						$attributes[$idx]['attributeType'] = $db_field_t;
					}
					else {
						http_response_code(500);
					}
			    }
				$attributes[$idx]['name']  = $name;
				
				$idx  = $idx  + 1;
				
				
			}
		}
		$con->close();

		$newArr[0]['attribute_assoc'] = $attributes;
		echo json_encode($newArr);
	}
}


?>