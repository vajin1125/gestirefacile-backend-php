<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

$oid_user = $currentUser['oid'];

if(isset($_GET['oidType'])) {
	$oidType = $_GET['oidType'];
	$sql = "SELECT * FROM attributes where oid_attribute_type='{$oidType}' and oid_user={$oid_user}";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		$idx = 0;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
			$oidBusiness = $db_field['oid_business'];
			if ($oidBusiness != NULL) {
				$sqlb = "SELECT * FROM business where oid={$oidBusiness}";
					
				if($resultB = mysqli_query($con,$sqlb)) {
					if ($db_field_b = mysqli_fetch_assoc($resultB))  {
						$newArr[$idx]['business'] = $db_field_b;
					}
					else {
						http_response_code(500);
					}
				}
			}
			$idx = $idx + 1;
		}
		$con->close();
		echo json_encode($newArr);
	}
}


?>