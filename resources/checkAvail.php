<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

$oid_user = $currentUser['oid'];


if(isset($_GET['oid']) && isset($_GET['start']) && isset($_GET['end'])) {
	$oidResource = $_GET['oid'];
	$start = $_GET['start'];
	$end = $_GET['end'];
	if ($_GET['oidEvent'] != '') {
		$oidEvent = $_GET['oidEvent'];
		$sql = "SELECT IFNULL(sum(ed.qta),0) as qta FROM events e, event_detail ed where e.oid = ed.oid_event  and e.oid_business in (select oid_business from role_user_assoc where oid_user={$oid_user}) and ed.oid_resource={$oidResource} and CAST('{$start}' AS DATETIME) <= e.to and CAST('{$end}' AS DATETIME) >= e.from and e.oid != {$oidEvent} and e.oid_event_status in (2,3)";
	}
	else {
		$sql = "SELECT IFNULL(sum(ed.qta),0) as qta FROM events e, event_detail ed where e.oid = ed.oid_event  and e.oid_business in (select oid_business from role_user_assoc where oid_user={$oid_user}) and ed.oid_resource={$oidResource} and CAST('{$start}' AS DATETIME) <= e.to and CAST('{$end}' AS DATETIME) >= e.from and e.oid_event_status in (2,3)";
	}

	
	
	
	$found = 0;
	if($result = mysqli_query($con,$sql)) {
		if ($db_field = mysqli_fetch_assoc($result))  {
			$found = $db_field['qta'];
		}
	}
	
	
	
	$con->close();
	
	echo $found;
	
}
else
{
	//http_response_code(200);
	//header( "HTTP/1.1 200 OK" );
	http_response_code(500);
	exit;
}