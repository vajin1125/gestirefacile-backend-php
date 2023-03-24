<?php
// database connection will be here
require '../config/security.php';


if (!empty($_POST)) 
{
	
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
	
	$sql = "SELECT max(oid)+1 as id from vendors ";
	
	if($result = mysqli_query($con,$sql)) {
		if ($row = mysqli_fetch_row($result))  {
			$oid = $row[0];
		}
		else {
			http_response_code(500);
		}
	}
	if ($oid == NULL) {
		$oid = 0;
	}
	
	
	$sql = "INSERT INTO `vendors` (`oid`, `oid_user`, `name`, `surname`, `email`, `address`, `business_name`, `tel`, `cell`, `piva`, `iban`, `pec`) VALUES ('{$oid}','{$oid_user}', '{$name}','{$surname}', '{$email}','{$address}', '{$business_name}', '{$tel}', '{$cell}','{$piva}', '{$iban}', '{$pec}')";
	if(mysqli_query($con,$sql))
	{
		
		$con->close();
		
		
		$_POST['oid'] = $oid;
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