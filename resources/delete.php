<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

if (!empty($_POST)) 
{

	$oid = $_POST['oid'];

	
	$sql = "DELETE FROM resource_resources_assoc where oid_resource =  '{$oid}'";
	if (mysqli_query($con,$sql)) {
		
	}
	
	$sql = "DELETE FROM category_resource_assoc where oid_resource =  '{$oid}'";
	if (mysqli_query($con,$sql)) {
		
	}
	$sql = "DELETE FROM resource_skill_assoc where oid_resource =  '{$oid}'";
	if (mysqli_query($con,$sql)) {
		
	}
	
	
	$sql = "DELETE FROM prices where oid_resource =  '{$oid}'";
	if (mysqli_query($con,$sql)) {
		
	}
	
	$sql = "DELETE FROM warehouse_movements where oid_resource =  '{$oid}'";
	if (mysqli_query($con,$sql)) {
		
	}
	
	$sql = "DELETE FROM role_user_assoc where oid_user in (select oid_user_res from resources where oid = '{$oid}')";
	if (mysqli_query($con,$sql)) {
		
	}
	
	$sql = "DELETE FROM users where oid in (select oid_user_res from resources where oid = '{$oid}')";
	if (mysqli_query($con,$sql)) {
		
	}
	
	$sql = "DELETE FROM event_detail where oid_resource =  '{$oid}'";
	if (mysqli_query($con,$sql)) {
		
	}
	
	$sql = "DELETE FROM package_detail where oid_resource =  '{$oid}'";
	if (mysqli_query($con,$sql)) {
		
	}
	
	$sql = "DELETE FROM resources WHERE `oid`='{$oid}'";
	if (mysqli_query($con,$sql)) {
		
	}
	
	
	$con->close();
	echo json_encode($_POST);

	
}
else
{
	//http_response_code(200);
	//header( "HTTP/1.1 200 OK" );
	http_response_code(500);
	exit;
}

?>