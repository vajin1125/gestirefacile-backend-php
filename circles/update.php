<?php
// database connection will be here
require '../config/security.php';


if (!empty($_POST)) 
{

	
	
	$oid = mysqli_real_escape_string($con, trim($_POST['oid']));
	$acronym = mysqli_real_escape_string($con, trim($_POST['acronym']));	
	$descr = mysqli_real_escape_string($con, trim($_POST['descr']));
	
	
	$sql = "UPDATE `circles` SET `acronym` = '{$acronym}', `descr` = '{$descr}'  WHERE `circles`.`oid` = '{$oid}'";
	if(mysqli_query($con,$sql))
	{
		
		$sql = "DELETE FROM circle_customer_assoc where oid_circle =  '{$oid}'";
		
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
			   
			   $sql = "INSERT INTO `circle_customer_assoc` (`oid`,`oid_circle`,`oid_customer`) VALUES ('{$oidCustomerAssoc}', '{$oid}', '{$value->customer->oid}')";
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