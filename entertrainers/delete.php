<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data) 
{
	$oid = $data['id'];
	$sql = "DELETE FROM entertrainer_availabilities WHERE oid={$oid}";
    
	if (mysqli_query($con, $sql)) 
    {
    }
	
	$con->close();
    echo json_encode($data);
	
}
else
{
	//http_response_code(200);
	//header( "HTTP/1.1 200 OK" );
	http_response_code(500);
	exit;
}

?>