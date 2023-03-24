<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$request = json_decode($postdata);
	
	
	$acronym = mysqli_real_escape_string($con, trim($request->acronym));
	$descr = mysqli_real_escape_string($con, trim($request->descr));
	$oid_user = $currentUser['oid'];
	
	
	
	$sql = "SELECT max(oid)+1 as id from event_type ";
	
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
	
	
	$sql = "INSERT INTO `event_type` (`oid`, `acronym`, `descr`, `oid_user`) VALUES ('{$oid}','{$acronym}', '{$descr}', {$oid_user})";
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