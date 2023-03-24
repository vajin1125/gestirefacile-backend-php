<?php
// database connection will be here
require '../config/core.php';
require '../config/database.php';
require 'recaptcha.php';

$fields = file_get_contents('php://input');

// error_log($fields);

if(isset($fields) && !empty($fields)){
	
	
	// var_dump(json_decode($fields));
	$issuedAt   = time();
	$notBefore  = $issuedAt + 10;//Adding 10 seconds
	$expire     = $notBefore +  ((60 * 60 * 24)*$session_day);   
	
	
	$dataSql = json_decode(file_get_contents("php://input"), true);
	$username = json_encode($dataSql['username']);
	$password = json_encode($dataSql['password']);

	
	
	$object = new Recaptcha();
	$response = $object->verifyResponse($dataSql['recaptcha']);

	if(isset($response['success']) and $response['success'] != true) {
		http_response_code(500);
		exit;
	}
	


	$sql = "SELECT * FROM users WHERE (username = ".$username." or email = ".$username.") and enabled=1";


	if($result = mysqli_query($con,$sql))
	{
	  
	  $i = 0;
	  if($row = mysqli_fetch_assoc($result))
	  {

		if (trim($password, '"') == $row["password"]) {
			
			

			/*$jwt_token = getJwt($data, $secretkey);
			
			if (checkJwt($jwt_token, $secretkey) ) {
			
			$obj =  json_decode($fields);*/
			$oid = $row['oid'];
			$obj = json_decode('{}');
			$obj->oid = $oid;
			$obj->name = $row['name'];
			$obj->surname = $row['surname'];
			$obj->password = "";
			$obj->email = $row['email'];
			$obj->maxbusinessnum = $row['max_business_num'];
			$obj->changepassword = $row['change_password'];
			$obj->image = $row['image'];
			if ($row['superuser'] == 1) {
				$obj->superuser = true;
			}
			else {
				$obj->superuser = false;
			}
			if ($row['enabled'] == 1) {
				$obj->enabled = true;
			}
			else {
				$obj->enabled = false;
			}
			
			$sql = "SELECT oid, oid_business, oid_role FROM role_user_assoc where oid_user='{$oid}'";
			$role = array();
			$idx = 0;
			if($result = mysqli_query($con,$sql)) {
				while ($row = mysqli_fetch_row($result))  {
					$oidRoleAssoc = $row[0];
					$oidBusiness = $row[1];
					$oidRole = $row[2];	
					
					$sqlb = "SELECT * FROM business where oid='{$oidBusiness}'";
					
					if($resultB = mysqli_query($con,$sqlb)) {
						if ($db_field_b = mysqli_fetch_assoc($resultB))  {
							$role[$idx]['business'] = $db_field_b;
						}
						else {
							http_response_code(500);
						}
					}
					
					$sqlr = "SELECT * FROM roles where oid='{$oidRole}'";
					if($resultR = mysqli_query($con,$sqlr)) {
						if ($db_field_r = mysqli_fetch_assoc($resultR))  {
							$role[$idx]['role'] = $db_field_r;
						}
						else {
							http_response_code(500);
						}
					}
					$idx  = $idx  + 1;
				}
			}
			$obj->role_user_assoc = $role;
			
			
			
			$data = [
			'iat'  => $issuedAt,         // Issued at: time when the token was generated
			'nbf'  => $notBefore,        // Not before
			'exp'  => $expire,           // Expire
			'data' => $obj				 // Data obj
			];
			$jwt_token = getJwt($data, $secretkey);
			if (checkJwt($jwt_token, $secretkey) ) {
				$obj->token = $jwt_token;
			}
			else {
				//header( "HTTP/1.1 200 OK" );
				$con->close();
				http_response_code(401);
				exit;
			}
			$data_string = json_encode($obj);
			
			$headers = getallheaders();  
			$latitude = $headers['Latitude'];
			$longitude = $headers['Longitude'];
			$fcmtoken = $headers['Token']; //FCM REGISTRATION ID TOKEN
			$ip = getIPAddress();
			
			/* POSITION MANAGER */
			if ($latitude != 0.00000000 && $longitude!= 0.00000000) { //POSITION AND IPADDRESS
				$oidUser = $obj->oid;
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
						echo $sql;
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
						echo $sql;
						echo("Error description: " . mysqli_error($con));
						http_response_code(422);
					}
				}
			} else {//ONLY IPADDRESS
				$oidUser = $obj->oid;
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
						echo $sql;
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
						echo $sql;
						echo("Error description: " . mysqli_error($con));
						http_response_code(422);
					}
				}
			}
			
			/* TOKEN DEVICE */
			if ($fcmtoken != null && $fcmtoken != "") {
				$oidUser = $obj->oid;
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
						echo $sql;
						echo("Error description: " . mysqli_error($con));
						http_response_code(422);
					}
				}
			}
			
			$con->close();
			echo $data_string;
			/*}
			else {
				//header( "HTTP/1.1 200 OK" );
				http_response_code(401);
				exit;
			}*/
		} else {
				//header( "HTTP/1.1 200 OK" );
				http_response_code(401);
				exit;
		}
		
		
	  }
	}
	
}
else
{
	//header( "HTTP/1.1 200 OK" );
	http_response_code(500);
	exit;
}


?>