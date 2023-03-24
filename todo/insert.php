<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$request = json_decode($postdata);
	
	
	$title = mysqli_real_escape_string($con, trim($request->title));
	$descr = mysqli_real_escape_string($con, trim($request->descr));
	$datetime = mysqli_real_escape_string($con, trim($request->datetime));
	
	
	$oid_user = $currentUser['oid'];
	//$oid_business = $request->business->oid;
	if ($request->completed == 'true') {
		$completed = 1;
	}
	else {
		$completed = 0;
	}
	
	$sql = "SELECT max(oid)+1 as id from todolist ";
	
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
	
	
	//$sql = "INSERT INTO `todolist` (`oid`, `title`, `descr`, `datetime`, `oid_user`, `oid_business`, `completed`) VALUES ('{$oid}','{$title}', '{$descr}', '{$datetime}', {$oid_user}, {$oid_business}, {$completed})";
	$sql = "INSERT INTO `todolist` (`oid`, `title`, `descr`, `datetime`, `oid_user`, `completed`) VALUES ('{$oid}','{$title}', '{$descr}', '{$datetime}', {$oid_user}, {$completed})";
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