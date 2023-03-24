<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';
require '../config/superuser.php';
	


	$sql = "SELECT * FROM access_log order by creation_ts desc";

	if($result = mysqli_query($con,$sql))
	{
		$idx = 0;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
			$oidUser = $db_field['oid_user'];
			$sqlu = "SELECT * FROM users where oid='{$oidUser}'";
			if($resultU = mysqli_query($con,$sqlu)) {
				if ($db_field_u = mysqli_fetch_assoc($resultU))  {
					$user = $db_field_u;
					$newArr[$idx]['user'] = $user;
				}
				else {
					http_response_code(500);
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