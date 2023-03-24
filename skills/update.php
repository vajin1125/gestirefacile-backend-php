<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$request = json_decode($postdata);

	$oid = mysqli_real_escape_string($con, trim($request->oid));
	$acronym = mysqli_real_escape_string($con, trim($request->acronym));
	$descr = mysqli_real_escape_string($con, trim($request->descr));
	
	
	$sql = "UPDATE `skills` SET `acronym` = '{$acronym}', `descr` = '{$descr}' WHERE `skills`.`oid` = {$oid}";
	
	
	if(mysqli_query($con,$sql))
	{
		$con->close();
		echo json_encode($request);
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