<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


$oid_user = $currentUser['oid'];

if(isset($_GET['oid'])) {
	$oid = $_GET['oid'];
	$sql = "SELECT * FROM plans where oid={$oid}";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
		}
		
		$sql = "SELECT * FROM plans_details where oid_plan={$oid}";
		$details = array();
		$idx = 0;
		if($result = mysqli_query($con,$sql)) {
			while ($db_field = mysqli_fetch_assoc($result))  {
				$details[$idx]['details'] = $db_field;
				$idx  = $idx  + 1;
			}
		}
		$newArr[0]['details'] = $details;
		
		
		
		$con->close();
		echo json_encode($newArr);

	}
}
else {
	$sql = "SELECT * FROM plans order by oid desc";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		$idx = 0;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
			$oid_plan = $db_field['oid'];
			
			
			$sqlp = "SELECT * FROM plans_details where oid_plan={$oid_plan}";
			$details = array();
			$idxP = 0;
			if($resultP = mysqli_query($con,$sqlp)) {
				while ($db_field_p = mysqli_fetch_assoc($resultP))  {
					$details[$idxP]['details'] = $db_field_p;
					$idxP  = $idxP  + 1;
				}
			}
			$newArr[$idx]['details'] = $details;
			$idx = $idx + 1;
		}
		$con->close();
		echo json_encode($newArr);

	}
}


?>