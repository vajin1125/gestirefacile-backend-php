<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

$oid_user = $currentUser['oid'];

if(isset($_GET['oid'])) {
	$oid = $_GET['oid'];
	$sql = "SELECT * FROM customers where oid='{$oid}' and oid_user={$oid_user}";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
		}
		
		$sql = "SELECT oid, oid_circle FROM circle_customer_assoc where oid_customer='{$oid}'";
		$circles = array();
		$idx = 0;
		if($result = mysqli_query($con,$sql)) {
			while ($row = mysqli_fetch_row($result))  {
				$oidAssoc = $row[0];
				$oidCircle = $row[1];
				
				$sqlc = "SELECT * FROM circles where oid='{$oidCircle}'";
				
				if($resultC = mysqli_query($con,$sqlc)) {
					if ($db_field_c = mysqli_fetch_assoc($resultC))  {
						$circles[$idx]['circle'] = $db_field_c;
					}
					else {
						http_response_code(500);
					}
			    }
				$idx  = $idx  + 1;
			}
		}
		$newArr[0]['circle_customer_assoc'] = $circles;
		
		
		$sql = "SELECT * FROM customer_attribute where oid_customer={$oid}";
		$attributes = array();
		
		if($result = mysqli_query($con,$sql)) {
			while ($row = mysqli_fetch_assoc($result))  {
				$attributes[] = $row;
			}
		}
		$newArr[0]['attribute_assoc'] = $attributes;
		
		
		
		
		$con->close();
		echo json_encode($newArr);

	}
}
else {
	$sql = "SELECT * FROM customers where oid_user={$oid_user}  order by oid desc";

	if($result = mysqli_query($con,$sql))
	{	$idx = 0;
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
			$oid = $db_field['oid'];
			
			$sqlAss = "SELECT oid, oid_circle FROM circle_customer_assoc where oid_customer='{$oid}'";
			$circles = array();
			$index = 0;
			if($resultAss = mysqli_query($con,$sqlAss)) {
				while ($rowAss = mysqli_fetch_row($resultAss))  {
					$oidAssoc = $rowAss[0];
					$oidCircle = $rowAss[1];
					
					$sqlc = "SELECT * FROM circles where oid='{$oidCircle}'";
					
					if($resultC = mysqli_query($con,$sqlc)) {
						if ($db_field_c = mysqli_fetch_assoc($resultC))  {
							$circles[$index]['circle'] = $db_field_c;
						}
						else {
							http_response_code(500);
						}
					}
					$index  = $index  + 1;
				}
			}
			$newArr[$idx]['circle_customer_assoc'] = $circles;
			
			
			$sqlA = "SELECT * FROM customer_attribute where oid_customer={$oid}";
			$attributes = array();
			
			if($resultA = mysqli_query($con,$sqlA)) {
				while ($rowA = mysqli_fetch_assoc($resultA))  {
					$attributes[] = $rowA;
				}
			}
			$newArr[$idx]['attribute_assoc'] = $attributes;
			$idx = $idx +1;
		}
		$con->close();
		echo json_encode($newArr);

	}
}


?>