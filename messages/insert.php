<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';
require '../push/push.php';
require '../config/superuser.php';

$postdata = file_get_contents("php://input");
if(isset($postdata) && !empty($postdata))
{
	$request = json_decode($postdata);
	
	$oid_user_create = mysqli_real_escape_string($con, trim($request->oid_user_create));
	$title = mysqli_real_escape_string($con, trim($request->title));
	$text = mysqli_real_escape_string($con, trim($request->text));
	$url = mysqli_real_escape_string($con, trim($request->url));
	$business = $request->business;
	$role = $request->role;
	$user_sent = $request->user_sent;
	
	$oid_business = 'NULL';
	if ($business->oid != "") {
		$oid_business = $business->oid;
	}
	$oid_role = 'NULL';
	if ($role->oid != "") {
		$oid_role = $role->oid;
	}
	$oid_user_sent = 'NULL';
	if ($user_sent->oid != "") {
		$oid_user_sent = $user_sent->oid;
	}

	
	$sql = "SELECT max(oid)+1 as id from messages ";
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
	
	
	
	$sql = "INSERT INTO `messages` (`oid`, `oid_user_create`, `title`, `text`, `url`, `creation_ts`, `oid_business`, `oid_role`, `oid_user_sent`, `sent_ts`) VALUES ('{$oid}','{$oid_user_create}', '{$title}', '{$text}','{$url}', now(), {$oid_business}, {$oid_role}, {$oid_user_sent}, null)";
	
	if(mysqli_query($con,$sql))
	{
		
		
		if ($oid_business != 'NULL' && $oid_role != 'NULL' && $oid_user_sent == 'NULL') { // per tutti i device di tutti gli utenti di un'attività che hanno quel ruolo
			$sql = "SELECT d.* FROM `role_user_assoc` rua, `devices` d WHERE rua.oid_business = '{$oid_business}' AND rua.oid_role = '{$oid_role}' AND d.oid_user = rua.oid_user";

			if($result = mysqli_query($con,$sql))
			{
				while ($db_field = mysqli_fetch_assoc($result)) {
					$oid_device = $db_field['oid'];
					$device_token = $db_field['token'];
					$postRequest = [
						"notification" => [
							"title" =>  "{$title}",
							"body" =>  "{$text}",
							"icon" =>  "https://www.gestirefacile.it/apple-touch-icon.png",
							"click_action" =>  "{$url}"
						],
						//"to" => "ccYIOzPZNaiWrkKPDNAqFL:APA91bF-jQvDkv-j4ztxnZakejk2H1fz82UAW9Dkd1eR8y-U8Y56jOIJg1BSwGxhOn9TMPg6XxHTMZVkrGTGNxBy9xt-LmDKx4SNsycJVu_sq1VXYso_2lGCvraOVWHruqTxbmq_6XH7",
						"to" => "{$device_token}"/*,
						'registration_ids' => array (
								$device_id
						),*/
						/*"data" => [
						  "Nick" => "Mario",
						  "Room" => "PortugalVSDenmark"
						]*/
						];

					$push = new Push();
					$response = $push->send($postRequest);
					//echo json_encode($response);//da commentare
					if ($response['success'] == 1) {
						$payload = json_encode($postRequest);
						
						$sql = "SELECT max(oid)+1 as id from message_device_assoc ";
						$oidMsgAssoc = -1;
						if($resultAssoc = mysqli_query($con,$sql)) {
							if ($rowAssoc = mysqli_fetch_row($resultAssoc))  {
								$oidMsgAssoc = $rowAssoc[0];
							}
							else {
								http_response_code(500);
							}
						}
						if ($oidMsgAssoc == NULL) {
							$oidMsgAssoc = 0;
						}
							
						$sql = "INSERT INTO `message_device_assoc` (`oid`, `oid_message`, `oid_device`, `payload`) VALUES ('{$oidMsgAssoc}','{$oid}', '{$oid_device}', '{$payload}')";
						if(mysqli_query($con,$sql)){		
							// insert ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}
					}
					else { // delete device token
						/*$sql = "delete from devices WHERE oid='{$oid_device}'";
						if(mysqli_query($con,$sql)){		
							// delete device ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}*/
					}
				}
			}
		}
		if ($oid_business != 'NULL' && $oid_role == 'NULL' && $oid_user_sent == 'NULL') { // per tutti i device degli utenti di un'attività a prescindere dal ruolo
			$sql = "SELECT d.* FROM `role_user_assoc` rua, `devices` d WHERE rua.oid_business = '{$oid_business}' AND d.oid_user = rua.oid_user";

			if($result = mysqli_query($con,$sql))
			{
				while ($db_field = mysqli_fetch_assoc($result)) {
					$oid_device = $db_field['oid'];
					$device_token = $db_field['token'];
					$postRequest = [
						"notification" => [
							"title" =>  "{$title}",
							"body" =>  "{$text}",
							"icon" =>  "https://www.gestirefacile.it/apple-touch-icon.png",
							"click_action" =>  "{$url}"
						],
						//"to" => "ccYIOzPZNaiWrkKPDNAqFL:APA91bF-jQvDkv-j4ztxnZakejk2H1fz82UAW9Dkd1eR8y-U8Y56jOIJg1BSwGxhOn9TMPg6XxHTMZVkrGTGNxBy9xt-LmDKx4SNsycJVu_sq1VXYso_2lGCvraOVWHruqTxbmq_6XH7",
						"to" => "{$device_token}"/*,
						'registration_ids' => array (
								$device_id
						),*/
						/*"data" => [
						  "Nick" => "Mario",
						  "Room" => "PortugalVSDenmark"
						]*/
						];

					$push = new Push();
					$response = $push->send($postRequest);
					//echo json_encode($response);//da commentare
					if ($response['success'] == 1) {
						$payload = json_encode($postRequest);
						
						$sql = "SELECT max(oid)+1 as id from message_device_assoc ";
						$oidMsgAssoc = -1;
						if($resultAssoc = mysqli_query($con,$sql)) {
							if ($rowAssoc = mysqli_fetch_row($resultAssoc))  {
								$oidMsgAssoc = $rowAssoc[0];
							}
							else {
								http_response_code(500);
							}
						}
						if ($oidMsgAssoc == NULL) {
							$oidMsgAssoc = 0;
						}
						
						$sql = "INSERT INTO `message_device_assoc` (`oid`, `oid_message`, `oid_device`, `payload`) VALUES ('{$oidMsgAssoc}','{$oid}', '{$oid_device}', '{$payload}')";
						if(mysqli_query($con,$sql)){		
							// insert ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}
					}
					else { // delete device token
						/*$sql = "delete from devices WHERE oid='{$oid_device}'";
						if(mysqli_query($con,$sql)){		
							// delete device ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}*/
					}
				}
			}
		}
		if ($oid_business != 'NULL' && $oid_role != 'NULL' && $oid_user_sent != 'NULL') { // per tutti i device di un utente di un'attività che ha quel ruolo
			$sql = "SELECT d.* FROM `role_user_assoc` rua, `devices` d WHERE rua.oid_business = '{$oid_business}' AND rua.oid_role = '{$oid_role}' AND rua.oid_user='{$oid_user_sent}' AND d.oid_user = rua.oid_user";
			
			if($result = mysqli_query($con,$sql))
			{
				while ($db_field = mysqli_fetch_assoc($result)) {
					$oid_device = $db_field['oid'];
					$device_token = $db_field['token'];
					$postRequest = [
						"notification" => [
							"title" =>  "{$title}",
							"body" =>  "{$text}",
							"icon" =>  "https://www.gestirefacile.it/apple-touch-icon.png",
							"click_action" =>  "{$url}"
						],
						//"to" => "ccYIOzPZNaiWrkKPDNAqFL:APA91bF-jQvDkv-j4ztxnZakejk2H1fz82UAW9Dkd1eR8y-U8Y56jOIJg1BSwGxhOn9TMPg6XxHTMZVkrGTGNxBy9xt-LmDKx4SNsycJVu_sq1VXYso_2lGCvraOVWHruqTxbmq_6XH7",
						"to" => "{$device_token}"/*,
						'registration_ids' => array (
								$device_id
						),*/
						/*"data" => [
						  "Nick" => "Mario",
						  "Room" => "PortugalVSDenmark"
						]*/
						];

					$push = new Push();
					$response = $push->send($postRequest);
					//echo json_encode($response);//da commentare
					if ($response['success'] == 1) {
						$payload = json_encode($postRequest);
						
						$sql = "SELECT max(oid)+1 as id from message_device_assoc ";
						$oidMsgAssoc = -1;
						if($resultAssoc = mysqli_query($con,$sql)) {
							if ($rowAssoc = mysqli_fetch_row($resultAssoc))  {
								$oidMsgAssoc = $rowAssoc[0];
							}
							else {
								http_response_code(500);
							}
						}
						if ($oidMsgAssoc == NULL) {
							$oidMsgAssoc = 0;
						}
						
						$sql = "INSERT INTO `message_device_assoc` (`oid`, `oid_message`, `oid_device`, `payload`) VALUES ('{$oidMsgAssoc}','{$oid}', '{$oid_device}', '{$payload}')";
						if(mysqli_query($con,$sql)){		
							// insert ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}
					}
					else { // delete device token
						/*$sql = "delete from devices WHERE oid='{$oid_device}'";
						if(mysqli_query($con,$sql)){		
							// delete device ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}*/
					}
				}
			}
		}
		if ($oid_business == 'NULL' && $oid_role != 'NULL' && $oid_user_sent == 'NULL') { // per tutti i device degli utenti che hanno quel ruolo a prescindere dall'attività
			$sql = "SELECT d.* FROM `role_user_assoc` rua, `devices` d WHERE  rua.oid_role = '{$oid_role}'  AND d.oid_user = rua.oid_user";
			
			if($result = mysqli_query($con,$sql))
			{
				while ($db_field = mysqli_fetch_assoc($result)) {
					$oid_device = $db_field['oid'];
					$device_token = $db_field['token'];
					$postRequest = [
						"notification" => [
							"title" =>  "{$title}",
							"body" =>  "{$text}",
							"icon" =>  "https://www.gestirefacile.it/apple-touch-icon.png",
							"click_action" =>  "{$url}"
						],
						//"to" => "ccYIOzPZNaiWrkKPDNAqFL:APA91bF-jQvDkv-j4ztxnZakejk2H1fz82UAW9Dkd1eR8y-U8Y56jOIJg1BSwGxhOn9TMPg6XxHTMZVkrGTGNxBy9xt-LmDKx4SNsycJVu_sq1VXYso_2lGCvraOVWHruqTxbmq_6XH7",
						"to" => "{$device_token}"/*,
						'registration_ids' => array (
								$device_id
						),*/
						/*"data" => [
						  "Nick" => "Mario",
						  "Room" => "PortugalVSDenmark"
						]*/
						];

					$push = new Push();
					$response = $push->send($postRequest);
					//echo json_encode($response);//da commentare
					if ($response['success'] == 1) {
						$payload = json_encode($postRequest);
						
						$sql = "SELECT max(oid)+1 as id from message_device_assoc ";
						$oidMsgAssoc = -1;
						if($resultAssoc = mysqli_query($con,$sql)) {
							if ($rowAssoc = mysqli_fetch_row($resultAssoc))  {
								$oidMsgAssoc = $rowAssoc[0];
							}
							else {
								http_response_code(500);
							}
						}
						if ($oidMsgAssoc == NULL) {
							$oidMsgAssoc = 0;
						}
						
						$sql = "INSERT INTO `message_device_assoc` (`oid`, `oid_message`, `oid_device`, `payload`) VALUES ('{$oidMsgAssoc}','{$oid}', '{$oid_device}', '{$payload}')";
						if(mysqli_query($con,$sql)){		
							// insert ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}
					}
					else { // delete device token
						/*$sql = "delete from devices WHERE oid='{$oid_device}'";
						if(mysqli_query($con,$sql)){		
							// delete device ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}*/
					}
				}
			}
		}
		if ($oid_business == 'NULL' && $oid_role != 'NULL' && $oid_user_sent != 'NULL') { // per tutti i device di un utente che ha quel ruolo a prescindere dall'attività
			$sql = "SELECT d.* FROM `role_user_assoc` rua, `devices` d WHERE rua.oid_role = '{$oid_role}' AND rua.oid_user='{$oid_user_sent}' AND d.oid_user = rua.oid_user";
			
			if($result = mysqli_query($con,$sql))
			{
				while ($db_field = mysqli_fetch_assoc($result)) {
					$oid_device = $db_field['oid'];
					$device_token = $db_field['token'];
					$postRequest = [
						"notification" => [
							"title" =>  "{$title}",
							"body" =>  "{$text}",
							"icon" =>  "https://www.gestirefacile.it/apple-touch-icon.png",
							"click_action" =>  "{$url}"
						],
						//"to" => "ccYIOzPZNaiWrkKPDNAqFL:APA91bF-jQvDkv-j4ztxnZakejk2H1fz82UAW9Dkd1eR8y-U8Y56jOIJg1BSwGxhOn9TMPg6XxHTMZVkrGTGNxBy9xt-LmDKx4SNsycJVu_sq1VXYso_2lGCvraOVWHruqTxbmq_6XH7",
						"to" => "{$device_token}"/*,
						'registration_ids' => array (
								$device_id
						),*/
						/*"data" => [
						  "Nick" => "Mario",
						  "Room" => "PortugalVSDenmark"
						]*/
						];

					$push = new Push();
					$response = $push->send($postRequest);
					//echo json_encode($response);//da commentare
					if ($response['success'] == 1) {
						$payload = json_encode($postRequest);
						
						$sql = "SELECT max(oid)+1 as id from message_device_assoc ";
						$oidMsgAssoc = -1;
						if($resultAssoc = mysqli_query($con,$sql)) {
							if ($rowAssoc = mysqli_fetch_row($resultAssoc))  {
								$oidMsgAssoc = $rowAssoc[0];
							}
							else {
								http_response_code(500);
							}
						}
						if ($oidMsgAssoc == NULL) {
							$oidMsgAssoc = 0;
						}
						
						$sql = "INSERT INTO `message_device_assoc` (`oid`, `oid_message`, `oid_device`, `payload`) VALUES ('{$oidMsgAssoc}','{$oid}', '{$oid_device}', '{$payload}')";
						if(mysqli_query($con,$sql)){		
							// insert ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}
					}
					else { // delete device token
						/*$sql = "delete from devices WHERE oid='{$oid_device}'";
						if(mysqli_query($con,$sql)){		
							// delete device ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}*/
					}
				}
			}
		}
		if ($oid_business == 'NULL' && $oid_role == 'NULL' && $oid_user_sent != 'NULL') { // per tutti i device di un utente a prescindere da ruolo e attività
			$sql = "SELECT d.* FROM `devices` d WHERE d.oid_user ='{$oid_user_sent}'";
			
			if($result = mysqli_query($con,$sql))
			{
				while ($db_field = mysqli_fetch_assoc($result)) {
					$oid_device = $db_field['oid'];
					$device_token = $db_field['token'];
					$postRequest = [
						"notification" => [
							"title" =>  "{$title}",
							"body" =>  "{$text}",
							"icon" =>  "https://www.gestirefacile.it/apple-touch-icon.png",
							"click_action" =>  "{$url}"
						],
						//"to" => "ccYIOzPZNaiWrkKPDNAqFL:APA91bF-jQvDkv-j4ztxnZakejk2H1fz82UAW9Dkd1eR8y-U8Y56jOIJg1BSwGxhOn9TMPg6XxHTMZVkrGTGNxBy9xt-LmDKx4SNsycJVu_sq1VXYso_2lGCvraOVWHruqTxbmq_6XH7",
						"to" => "{$device_token}"/*,
						'registration_ids' => array (
								$device_id
						),*/
						/*"data" => [
						  "Nick" => "Mario",
						  "Room" => "PortugalVSDenmark"
						]*/
						];

					$push = new Push();
					$response = $push->send($postRequest);
					//echo json_encode($response);//da commentare
					if ($response['success'] == 1) {
						$payload = json_encode($postRequest);
						
						$sql = "SELECT max(oid)+1 as id from message_device_assoc ";
						$oidMsgAssoc = -1;
						if($resultAssoc = mysqli_query($con,$sql)) {
							if ($rowAssoc = mysqli_fetch_row($resultAssoc))  {
								$oidMsgAssoc = $rowAssoc[0];
							}
							else {
								http_response_code(500);
							}
						}
						if ($oidMsgAssoc == NULL) {
							$oidMsgAssoc = 0;
						}
						
						$sql = "INSERT INTO `message_device_assoc` (`oid`, `oid_message`, `oid_device`, `payload`) VALUES ('{$oidMsgAssoc}','{$oid}', '{$oid_device}', '{$payload}')";
						if(mysqli_query($con,$sql)){		
							// insert ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}
					}
					else { // delete device token
						/*$sql = "delete from devices WHERE oid='{$oid_device}'";
						if(mysqli_query($con,$sql)){		
							// delete device ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}*/
					}
				}
			}
		}
		if ($oid_business == 'NULL' && $oid_role == 'NULL' && $oid_user_sent == 'NULL') { // per tutti i device di tutti gli utente
			$sql = "SELECT * FROM `devices`";
			
			if($result = mysqli_query($con,$sql))
			{
				while ($db_field = mysqli_fetch_assoc($result)) {
					$oid_device = $db_field['oid'];
					$device_token = $db_field['token'];
					$postRequest = [
						"notification" => [
							"title" =>  "{$title}",
							"body" =>  "{$text}",
							"icon" =>  "https://www.gestirefacile.it/apple-touch-icon.png",
							"click_action" =>  "{$url}"
						],
						//"to" => "ccYIOzPZNaiWrkKPDNAqFL:APA91bF-jQvDkv-j4ztxnZakejk2H1fz82UAW9Dkd1eR8y-U8Y56jOIJg1BSwGxhOn9TMPg6XxHTMZVkrGTGNxBy9xt-LmDKx4SNsycJVu_sq1VXYso_2lGCvraOVWHruqTxbmq_6XH7",
						"to" => "{$device_token}"/*,
						'registration_ids' => array (
								$device_id
						),*/
						/*"data" => [
						  "Nick" => "Mario",
						  "Room" => "PortugalVSDenmark"
						]*/
						];

					$push = new Push();
					$response = $push->send($postRequest);
					//echo json_encode($response);//da commentare
					if ($response['success'] == 1) {
						$payload = json_encode($postRequest);
						
						$sql = "SELECT max(oid)+1 as id from message_device_assoc ";
						$oidMsgAssoc = -1;
						if($resultAssoc = mysqli_query($con,$sql)) {
							if ($rowAssoc = mysqli_fetch_row($resultAssoc))  {
								$oidMsgAssoc = $rowAssoc[0];
							}
							else {
								http_response_code(500);
							}
						}
						if ($oidMsgAssoc == NULL) {
							$oidMsgAssoc = 0;
						}
						
						$sql = "INSERT INTO `message_device_assoc` (`oid`, `oid_message`, `oid_device`, `payload`) VALUES ('{$oidMsgAssoc}','{$oid}', '{$oid_device}', '{$payload}')";
						if(mysqli_query($con,$sql)){		
							// insert ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}
					}
					else { // delete device token
						/*$sql = "delete from devices WHERE oid='{$oid_device}'";
						if(mysqli_query($con,$sql)){		
							// delete device ok
						}else{
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}*/
					}
				}
			}
		}

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