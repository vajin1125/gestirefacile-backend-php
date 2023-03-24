<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


$oid_user = $currentUser['oid'];


	$sql = "SELECT extra_descr FROM event_extras where oid_user={$oid_user} order by oid desc";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		$idx = 0;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
		}
		$con->close();
		echo json_encode($newArr);

	}



?>