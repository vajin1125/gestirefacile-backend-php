<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata))
{
	$request = json_decode($postdata);

	$oid = mysqli_real_escape_string($con, trim($request->oid));


	
	$sql = "DELETE FROM category_resource_assoc where oid_category =  '{$oid}'";
	if (mysqli_query($con,$sql)) {
		
	}
	
	
	$sql = "DELETE FROM categories where oid =  '{$oid}'";
	if (mysqli_query($con,$sql)) {
		
	}
	
	
	$con->close();
	echo json_encode($request);

	
}
else
{
	//http_response_code(200);
	//header( "HTTP/1.1 200 OK" );
	http_response_code(500);
	exit;
}

?>