<?php
// database connection will be here
require '../config/security.php';



if (!empty($_POST)) 
{

	
	$oid = mysqli_real_escape_string($con, trim($_POST['oid']));
	$name = mysqli_real_escape_string($con, trim($_POST['name']));	
	$surname = mysqli_real_escape_string($con, trim($_POST['surname']));
	$email = mysqli_real_escape_string($con, trim($_POST['email']));
	$address = mysqli_real_escape_string($con, trim($_POST['address']));
	$desc = mysqli_real_escape_string($con, trim($_POST['desc']));
	$tel = mysqli_real_escape_string($con, trim($_POST['tel']));
	$cell = mysqli_real_escape_string($con, trim($_POST['cell']));
	$piva = mysqli_real_escape_string($con, trim($_POST['piva']));
	$oid_user = $currentUser['oid'];
	
	
	
	$sql = "UPDATE `customers` SET `name` = '{$name}', `surname` = '{$surname}', `email` = '{$email}', `address` = '{$address}', `desc` = '{$desc}',  `tel` = '{$tel}', `cell` = '{$cell}', `piva` = '{$cell}'  WHERE `customers`.`oid` = '{$oid}'  and `customers`.`oid_user` = '{$oid_user}' ";
	
	
	if(mysqli_query($con,$sql))
	{
		
		$sql = "DELETE FROM circle_customer_assoc where oid_customer =  '{$oid}'";
		
		if (mysqli_query($con,$sql) && $_POST['circle_customer_assoc']) {
			$customerassoc = json_decode($_POST['circle_customer_assoc']);
			$_POST['circle_customer_assoc'] = $customerassoc;
			foreach ($customerassoc as $value)
			{
			   
			   $sql = "SELECT max(oid)+1 as id from `circle_customer_assoc` ";
	
			   if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidCustomerAssoc = $row[0];
					}
					else {
						http_response_code(500);
					}
			   }
				if ($oidCustomerAssoc == NULL) {
					$oidCustomerAssoc = 0;
				}			   
			   
			   $sql = "INSERT INTO `circle_customer_assoc` (`oid`,`oid_circle`,`oid_customer`) VALUES ('{$oidCustomerAssoc}', '{$value->circle->oid}', '{$oid}')";
			   if(mysqli_query($con,$sql))
			   {
				//OK
			   }
			   else {
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
			   }
			}
		}
		
		$sql = "DELETE FROM customer_attribute where oid_customer =  {$oid}";
		
		if (mysqli_query($con,$sql) && $_POST['attribute_assoc']) {
			$attributeassoc = json_decode($_POST['attribute_assoc']);
			$_POST['attribute_assoc'] = $attributeassoc;
			foreach ($attributeassoc as $value)
			{
				if ($value->value == '') {
					continue;
				}
			   
			    $sql = "SELECT max(oid)+1 as id from `customer_attribute` ";
	
			    if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidAttributeAssoc = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidAttributeAssoc == NULL) {
					$oidAttributeAssoc = 0;
				}
				
				$oidAttribute = 'NULL';
				if (isset($value->oid)) {
					$oidAttribute = $value->oid;
				}
				
				
				
			   
			    $sql = "INSERT INTO `customer_attribute` (`oid`,`oid_customer`,`oid_attribute`,`name`,`value`) VALUES ({$oidAttributeAssoc}, {$oid}, {$oidAttribute},'{$value->name}', '{$value->value}')";
			    if(mysqli_query($con,$sql))
			    {
				 //OK
			    }
			    else {
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
			    }
			}
		}
		
		
		$con->close();
		
		
		//echo json_encode($request);
		echo json_encode($_POST);
	}
	else
	{
		echo("Error description: " . mysqli_error($con));
		http_response_code(422);
	}
}
else
{
	//http_response_code(200);
	//header( "HTTP/1.1 200 OK" );
	http_response_code(500);
	exit;
}

?>