<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


$postdata = file_get_contents("php://input");



if(isset($postdata) && !empty($postdata))
{
	$request = json_decode($postdata);
	
	
	$oid_user = $currentUser['oid'];
	$oid_resource = $request->resource->oid;
	//$oid_type_movement = $request->movementType->oid;
	$qta = mysqli_real_escape_string($con, trim($request->qta));
	$reason = mysqli_real_escape_string($con, trim($request->reason));
	
	
	$sqlBus = "select oid_business from role_user_assoc where oid_user= {$oid_user}";
	if($resultBus = mysqli_query($con,$sqlBus)) {
		if ($rowBus = mysqli_fetch_row($resultBus))  {
			$oid_business = $rowBus[0];
			$sqlMag = "select oid from warehouse where oid_business={$oid_business}";
			if($resultMag = mysqli_query($con,$sqlMag)) {
				if ($rowMag = mysqli_fetch_row($resultMag))  {
					$oidMag = $rowMag[0];
					$sqlMov = "SELECT max(oid)+1 as id from warehouse_movements ";

					if($resultMov = mysqli_query($con,$sqlMov)) {
						if ($rowMov = mysqli_fetch_row($resultMov))  {
							$oidMov = $rowMov[0];
						}
						else {
							http_response_code(500);
						}
					}
					if ($oidMov == NULL) {
						$oidMov = 0;
					}	
					
					if ($qta >= 0) {
						$oid_type_movement = 1;
					}
					else {
						$oid_type_movement = 2;
					}
					
					$sqlInsMag = "INSERT INTO `warehouse_movements` (`oid`, `oid_event`, `oid_warehouse`, `oid_user_operation`, `oid_resource`, `oid_type_movement`, `qta`, `reason`, `date_ts`) VALUES ({$oidMov}, NULL, {$oidMag}, {$oid_user}, {$oid_resource}, {$oid_type_movement}, {$qta}, '{$reason}', now())";
					if(mysqli_query($con,$sqlInsMag))
					{
						
						
						$sqlUpdRes = "UPDATE resources set avail_qta = avail_qta + ({$qta}) where oid = {$oid_resource}";
						if(mysqli_query($con,$sqlUpdRes)) 
						{
							//UPDATE RESOURCE OK
							$request->oid = $oidMov;
							echo json_encode($request);
						}
						else {
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
						}
					}
					else {
						echo("Error description: " . mysqli_error($con));
						http_response_code(422);
					}
				}
			}
		}
	}
}
else
{
	//http_response_code(200);
	//header( "HTTP/1.1 200 OK" );
	echo "ERRORE 500";
	http_response_code(500);
	exit;
}
?>