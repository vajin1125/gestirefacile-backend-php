<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';



$sql = "SELECT * FROM attribute_type order by oid desc";

if($result = mysqli_query($con,$sql))
{
	$newArr = array();
	while ($db_field = mysqli_fetch_assoc($result)) {
		$newArr[] = $db_field;
	}
	$con->close();
	echo json_encode($newArr);

}



?>