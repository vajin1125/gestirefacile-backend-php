<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';



if(isset($_GET['oid'])) {
	$oid = $_GET['oid'];
	$sql = "SELECT * FROM business where oid='{$oid}'";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
		}
		$oid_user = $currentUser['oid'];
		$sql = "SELECT oid, oid_user, oid_role FROM role_user_assoc where oid_business='{$oid}'";
		$role = array();
		$idx = 0;
		if($result = mysqli_query($con,$sql)) {
			while ($row = mysqli_fetch_row($result))  {
				$oidRoleAssoc = $row[0];
				$oidUser= $row[1];
				$oidRole = $row[2];	
				
				$sqlu = "SELECT * FROM users where oid='{$oidUser}'";
				
				if($resultU = mysqli_query($con,$sqlu)) {
					if ($db_field_u = mysqli_fetch_assoc($resultU))  {
						$role[$idx]['user'] = $db_field_u;
					}
					else {
						http_response_code(500);
					}
			    }
				
				$sqlr = "SELECT * FROM roles where oid='{$oidRole}'";
				if($resultR = mysqli_query($con,$sqlr)) {
					if ($db_field_r = mysqli_fetch_assoc($resultR))  {
						$role[$idx]['role'] = $db_field_r;
					}
					else {
						http_response_code(500);
					}
			    }
				$idx  = $idx  + 1;
			}
		}
		$newArr[0]['role_user_assoc'] = $role;
		
		
		
		$con->close();
		echo json_encode($newArr);

	}
}
else {
	$oid_user = $currentUser['oid'];
	$sql = "SELECT b.* FROM business b, role_user_assoc rua, roles r where rua.oid_user='{$oid_user}' and rua.oid_business = b.oid and rua.oid_role = r.oid and r.acronym='MANAGER' and b.enabled = 1 order by b.oid desc";

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