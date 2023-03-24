<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

$oid_user = $currentUser['oid'];

if(isset($_GET['oid'])) {
	$oid = $_GET['oid'];
	$sql = "SELECT * FROM circles where oid='{$oid}' and oid_user={$oid_user}";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
		}
		
		$sql = "SELECT oid, oid_customer FROM circle_customer_assoc where oid_circle='{$oid}'";
		$customers = array();
		$idx = 0;
		if($result = mysqli_query($con,$sql)) {
			while ($row = mysqli_fetch_row($result))  {
				$oidAssoc = $row[0];
				$oidCustomer = $row[1];
				
				$sqlc = "SELECT * FROM customers where oid='{$oidCustomer}'";
				
				if($resultC = mysqli_query($con,$sqlc)) {
					if ($db_field_c = mysqli_fetch_assoc($resultC))  {
						$customers[$idx]['customer'] = $db_field_c;
					}
					else {
						http_response_code(500);
					}
			    }
				$idx  = $idx  + 1;
			}
		}
		$newArr[0]['circle_customer_assoc'] = $customers;
		
		
		
		$con->close();
		echo json_encode($newArr);

	}
}
else {
	$sql = "SELECT * FROM circles where oid_user={$oid_user}  order by oid desc";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		$idxCircle = 0;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
			$oid = $newArr[$idxCircle]['oid'];
			$sql = "SELECT oid, oid_customer FROM circle_customer_assoc where oid_circle='{$oid}'";
			$customers = array();
			$idx = 0;
			if($resultAssoc = mysqli_query($con,$sql)) {
				while ($row = mysqli_fetch_row($resultAssoc))  {
					$oidAssoc = $row[0];
					$oidCustomer = $row[1];
					
					$sqlc = "SELECT * FROM customers where oid='{$oidCustomer}'";
					
					if($resultC = mysqli_query($con,$sqlc)) {
						if ($db_field_c = mysqli_fetch_assoc($resultC))  {
							$customers[$idx]['customer'] = $db_field_c;
						}
						else {
							http_response_code(500);
						}
					}
					$idx  = $idx  + 1;
				}
			}
			$newArr[0]['circle_customer_assoc'] = $customers;
			$idxCircle = $idxCircle + 1;
		}
		
		
		$con->close();
		echo json_encode($newArr);

	}
}


?>