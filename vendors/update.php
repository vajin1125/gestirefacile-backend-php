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
	$business_name = mysqli_real_escape_string($con, trim($_POST['business_name']));
	$tel = mysqli_real_escape_string($con, trim($_POST['tel']));
	$cell = mysqli_real_escape_string($con, trim($_POST['cell']));
	$piva = mysqli_real_escape_string($con, trim($_POST['piva']));
  $iban = mysqli_real_escape_string($con, trim($_POST['iban']));
  $pec = mysqli_real_escape_string($con, trim($_POST['pec']));
	$oid_user = $currentUser['oid'];
	
	
	
	$sql = "UPDATE `vendors` SET `name` = '{$name}', `surname` = '{$surname}', `email` = '{$email}', `address` = '{$address}', `business_name` = '{$business_name}',  `tel` = '{$tel}', `cell` = '{$cell}', `piva` = '{$piva}', `iban` = '{$iban}', `pec` = '{$pec}'  WHERE `vendors`.`oid` = '{$oid}'  and `vendors`.`oid_user` = '{$oid_user}' ";
	
	
	if(mysqli_query($con,$sql))
	{
		
		
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