<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$request = json_decode($postdata);

	$oid = mysqli_real_escape_string($con, trim($request->oid));
	$title = mysqli_real_escape_string($con, trim($request->title));
	$descr = mysqli_real_escape_string($con, trim($request->descr));
	$datetime = mysqli_real_escape_string($con, trim($request->datetime));
	if ($request->completed == 'true') {
		$completed = 1;
	}
	else {
		$completed = 0;
	}
	
	
	$sql = "UPDATE `todolist` SET `title` = '{$title}', `descr` = '{$descr}', `datetime` = '{$datetime}', `completed` = '{$completed}' WHERE `todolist`.`oid` = '{$oid}'";
	
	
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