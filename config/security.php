<?php
require '../config/core.php';
require '../config/database.php';

$token = getBearerToken();

if ($token) {
	if (checkJwt($token, $secretkey)) { //CHECK VALIDITY JWT TOKEN
		//token valido vado avanti con l'esecuzione
		$headers = getallheaders();  
		$latitude = $headers['Latitude'];
		$longitude = $headers['Longitude'];
		$fcmtoken = $headers['Token']; //FCM REGISTRATION ID TOKEN
		$ip = getIPAddress();		
		$payload = getPayloadJwt($token);
		$currentUser =  $payload['data'];
		
		/* POSITION MANAGER */
		if ($latitude != 0.00000000 && $longitude!= 0.00000000) { //POSITION AND IPADDRESS
			$oidUser = $currentUser['oid'];
			$insertAccess= true;
			$sql = "SELECT oid from access_log where oid_user='{$oidUser}' and DATE(creation_ts)=DATE(SYSDATE()) and latitude='{$latitude}' and longitude='{$longitude}' and ip_address='{$ip}'";
			$oidAccessLog = -1;
			if($result = mysqli_query($con,$sql)) {
				if ($row = mysqli_fetch_row($result))  {
					$oidAccessLog = $row[0];
					$insertAccess = false;
				}
			}
			
			if ($insertAccess) {
				$sql = "SELECT max(oid)+1 as id from access_log";
	
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
				
				$sql = "INSERT INTO `access_log` (`oid`, `ip_address`, `creation_ts`, `oid_user`, `latitude`, `longitude`) VALUES ('{$oid}','{$ip}', SYSDATE(), '{$oidUser}', '{$latitude}', '{$longitude}')";
				if(mysqli_query($con,$sql))
				{
					/*$con->close();
					echo json_encode($request);*/
				}
				else
				{
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
				}
			}
			else {
				$sql = "UPDATE `access_log` set creation_ts=SYSDATE() where oid='{$oidAccessLog}'";
				if(mysqli_query($con,$sql))
				{
					//UPDATE DATE OK
				}
				else
				{
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
				}
			}
		} else {//ONLY IPADDRESS
			$oidUser = $currentUser['oid'];
			$insertAccess = true;
			$sql = "SELECT oid from access_log where oid_user='{$oidUser}' and DATE(creation_ts)=DATE(SYSDATE()) and ip_address='{$ip}'";
			$oidAccessLog = -1;
			if($result = mysqli_query($con,$sql)) {
				if ($row = mysqli_fetch_row($result))  {
					$oidAccessLog = $row[0];
					$insertAccess = false;
				}
			}
			
			if ($insertAccess) {
				$sql = "SELECT max(oid)+1 as id from access_log";
	
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
				
				$sql = "INSERT INTO `access_log` (`oid`, `ip_address`, `creation_ts`, `oid_user`, `latitude`, `longitude`) VALUES ('{$oid}','{$ip}', SYSDATE(), '{$oidUser}', 0, 0)";
				if(mysqli_query($con,$sql))
				{
					/*$con->close();
					echo json_encode($request);*/
				}
				else
				{
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
				}
			}
			else {
				$sql = "UPDATE `access_log` set creation_ts=SYSDATE() where oid='{$oidAccessLog}'";
				if(mysqli_query($con,$sql))
				{
					//UPDATE DATE OK
				}
				else
				{
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
				}
			}
		}
		
		/* TOKEN DEVICE */
		if ($fcmtoken != null && $fcmtoken != "") {
			$oidUser = $currentUser['oid'];
			$newToken = true;
			$sql = "SELECT oid from devices where oid_user='{$oidUser}' and token='{$fcmtoken}'";
			if($result = mysqli_query($con,$sql)) {
				if ($row = mysqli_fetch_row($result))  {
					$newToken = false;
				}
			}
			if ($newToken){
				$sql = "SELECT max(oid)+1 as id from devices";
	
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
				
				$sql = "INSERT INTO `devices` (`oid`, `oid_user`, `token`) VALUES ('{$oid}','{$oidUser}','{$fcmtoken}')";
				if(mysqli_query($con,$sql))
				{
					/*$con->close();
					echo json_encode($request);*/
				}
				else
				{
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
				}
			}
		}
		//$con->close();
	}
	else {
		echo 'Unauthorized invalid!';
		//header( "HTTP/1.1 200 OK" );
		http_response_code(401);
		exit;
	}
}
else {
	echo 'Unauthorized not found!';
	//header( "HTTP/1.1 200 OK" );
	http_response_code(401);
	exit;
}



?>