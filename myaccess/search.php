<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

	

	$oid_user = $currentUser['oid'];
	$sql = "SELECT * FROM access_log where oid_user in (SELECT u.oid FROM users u, role_user_assoc rua1 where  u.oid = rua1.oid_user and rua1.oid_business in (SELECT b.oid FROM business b, role_user_assoc rua, roles r where rua.oid_user='{$oid_user}' and rua.oid_business = b.oid and rua.oid_role = r.oid and r.acronym='MANAGER') UNION select oid from users where oid_user_ref = '{$oid_user}') order by creation_ts desc";

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