<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


$oid_user = $currentUser['oid'];

if(isset($_GET['oid'])) {
	$oid = $_GET['oid'];
	$sql = "SELECT * FROM packages where oid={$oid} and oid_user={$oid_user}";

	if($result = mysqli_query($con,$sql))
	{
		
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
		}
		
		$sqlD = "select `oid`, `oid_package`, `oid_resource_type`, `oid_category`, `oid_skill`, `oid_resource`, `qta`, `price`, `extra_descr`, `is_extra`, `note`, `hours`, `days`, `total_price`  from package_detail where oid_package={$oid} and is_extra=0";
		$event_details = array();
		$idxR = 0;
		if($resultD = mysqli_query($con,$sqlD)) {
			while ($row = mysqli_fetch_row($resultD))  {
				$oidResourceType = $row[2];
				$oidCategory = $row[3];
				$oidSkill = $row[4];
				$oidResource = $row[5];
				
				if ($oidResourceType) {
					$sqlRT = "SELECT * FROM resource_types where oid={$oidResourceType}";
				
					if($resultRT = mysqli_query($con,$sqlRT)) {
						if ($db_field_rt = mysqli_fetch_assoc($resultRT))  {
							$event_details[$idxR]['resourceType'] = $db_field_rt;
						}
						else {
							http_response_code(500);
						}
					}
				}
				
				if ($oidCategory) {
					$sqlC = "SELECT * FROM categories where oid={$oidCategory}";
				
					if($resultC = mysqli_query($con,$sqlC)) {
						if ($db_field_c = mysqli_fetch_assoc($resultC))  {
							$event_details[$idxR]['category'] = $db_field_c;
						}
						else {
							http_response_code(500);
						}
					}
				}
				
				
				if ($oidSkill) {
					$sqlS = "SELECT * FROM skills where oid={$oidSkill}";
				
					if($resultS = mysqli_query($con,$sqlS)) {
						if ($db_field_s = mysqli_fetch_assoc($resultS))  {
							$event_details[$idxR]['skill'] = $db_field_s;
						}
						else {
							http_response_code(500);
						}
					}
				}
				
				if ($oidResource) {
					$sqlR = "SELECT * FROM resources where oid={$oidResource}";
				
					if($resultR = mysqli_query($con,$sqlR)) {
						if ($db_field_r = mysqli_fetch_assoc($resultR))  {
							$oid_resource_type = $db_field_r['oid_resource_type'];
							$sqlt = "select * from resource_types where oid={$oid_resource_type}";
							$resourceType = null;
							if($resultT = mysqli_query($con,$sqlt)) {
								if ($db_field_t = mysqli_fetch_assoc($resultT))  {
									$resourceType = $db_field_t;
								}
								else {
									http_response_code(500);
								}
							}
							
							$sqlP = "SELECT * FROM prices where oid_resource={$oidResource}";
							$prices = array();
							$idxP = 0;
							if($resultP = mysqli_query($con,$sqlP)) {
								while ($db_field_p = mysqli_fetch_assoc($resultP))  {
									$prices[$idxP] = $db_field_p;
									$idxP  = $idxP  + 1;
								}
							}
							
							
							$event_details[$idxR]['resource'] = $db_field_r;
							$event_details[$idxR]['resource']['resourceType'] = $resourceType;
							$event_details[$idxR]['resource']['prices'] = $prices;
						}
						else {
							http_response_code(500);
						}
					}
				}
				
				$event_details[$idxR]['qta'] = $row[6];
				$event_details[$idxR]['price'] = $row[7];
				$event_details[$idxR]['is_extra'] = false;
				$event_details[$idxR]['note'] = $row[10];
				$event_details[$idxR]['hours'] = $row[11];
				$event_details[$idxR]['days'] = $row[12];
				$event_details[$idxR]['total_price'] = $row[13];
				
				$idxR  = $idxR  + 1;
			}
		}	
		$newArr[0]['package_details'] = $event_details;
		
		
		$sqlD = "select `oid`, `oid_package`, `oid_resource_type`, `oid_category`, `oid_skill`, `oid_resource`, `qta`, `price`, `extra_descr`, `is_extra`, `note`, `hours`, `days`, `total_price` from package_detail where oid_package={$oid} and is_extra=1";
		$extra_details = array();
		$idxR = 0;
		if($resultD = mysqli_query($con,$sqlD)) {
			while ($row = mysqli_fetch_row($resultD))  {
				
				
				$extra_details[$idxR]['qta'] = $row[6];
				$extra_details[$idxR]['price'] = $row[7];
				$extra_details[$idxR]['extra_descr'] = $row[8];
				$extra_details[$idxR]['is_extra'] = true;

				$extra_details[$idxR]['note'] = $row[10];
				$extra_details[$idxR]['hours'] = $row[11];
				$extra_details[$idxR]['days'] = $row[12];
				$extra_details[$idxR]['total_price'] = $row[13];
				
				$idxR  = $idxR  + 1;
			}
		}	
		$newArr[0]['extra_details'] = $extra_details;
		
		
		$con->close();
		echo json_encode($newArr);

	}
}
else {
	$sql = "SELECT * FROM packages where oid_user={$oid_user} order by oid desc";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		$idx = 0;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
			$oid_package = $db_field['oid'];
			
			
			$sqlD = "select `oid`, `oid_package`, `oid_resource_type`, `oid_category`, `oid_skill`, `oid_resource`, `qta`, `price`, `extra_descr`, `is_extra`, `note`, `hours`, `days` from package_detail where oid_package={$oid_package} and is_extra=0";
			$event_details = array();
			$idxR = 0;
			if($resultD = mysqli_query($con,$sqlD)) {
				while ($row = mysqli_fetch_row($resultD))  {
					$oidResourceType = $row[2];
					$oidCategory = $row[3];
					$oidSkill = $row[4];
					$oidResource = $row[5];
					
					if ($oidResourceType) {
						$sqlRT = "SELECT * FROM resource_types where oid={$oidResourceType}";
					
						if($resultRT = mysqli_query($con,$sqlRT)) {
							if ($db_field_rt = mysqli_fetch_assoc($resultRT))  {
								$event_details[$idxR]['resourceType'] = $db_field_rt;
							}
							else {
								http_response_code(500);
							}
						}
					}
					
					if ($oidCategory) {
						$sqlC = "SELECT * FROM categories where oid={$oidCategory}";
					
						if($resultC = mysqli_query($con,$sqlC)) {
							if ($db_field_c = mysqli_fetch_assoc($resultC))  {
								$event_details[$idxR]['category'] = $db_field_c;
							}
							else {
								http_response_code(500);
							}
						}
					}
					
					
					if ($oidSkill) {
						$sqlS = "SELECT * FROM skills where oid={$oidSkill}";
					
						if($resultS = mysqli_query($con,$sqlS)) {
							if ($db_field_s = mysqli_fetch_assoc($resultS))  {
								$event_details[$idxR]['skill'] = $db_field_s;
							}
							else {
								http_response_code(500);
							}
						}
					}
					if ($oidResource) {
						$sqlR = "SELECT * FROM resources where oid={$oidResource}";
				
						if($resultR = mysqli_query($con,$sqlR)) {
							if ($db_field_r = mysqli_fetch_assoc($resultR))  {
								$oid_resource_type = $db_field_r['oid_resource_type'];
								$sqlt = "select * from resource_types where oid={$oid_resource_type}";
								$resourceType = null;
								if($resultT = mysqli_query($con,$sqlt)) {
									if ($db_field_t = mysqli_fetch_assoc($resultT))  {
										$resourceType = $db_field_t;
									}
									else {
										http_response_code(500);
									}
								}
								
								$sqlP = "SELECT * FROM prices where oid_resource={$oidResource}";
								$prices = array();
								$idxP = 0;
								if($resultP = mysqli_query($con,$sqlP)) {
									while ($db_field_p = mysqli_fetch_assoc($resultP))  {
										$prices[$idxP] = $db_field_p;
										$idxP  = $idxP  + 1;
									}
								}
								
								
								$event_details[$idxR]['resource'] = $db_field_r;
								$event_details[$idxR]['resource']['resourceType'] = $resourceType;
								$event_details[$idxR]['resource']['prices'] = $prices;
							}
							else {
								http_response_code(500);
							}
						}
					}
				
					
					$event_details[$idxR]['qta'] = $row[6];
					$event_details[$idxR]['price'] = $row[7];
					$event_details[$idxR]['is_extra'] = false;
					$event_details[$idxR]['note'] = $row[10];
					$event_details[$idxR]['hours'] = $row[11];
					$event_details[$idxR]['days'] = $row[12];
					
					
					$idxR  = $idxR  + 1;
				}
			}	
			$newArr[$idx]['package_details'] = $event_details;
			
			
			$sqlD = "select `oid`, `oid_package`, `oid_resource_type`, `oid_category`, `oid_skill`, `oid_resource`, `qta`, `price`, `extra_descr`, `is_extra`, `note`, `hours`, `days` from package_detail where oid_package={$oid_package} and is_extra=1";
			$extra_details = array();
			$idxR = 0;
			if($resultD = mysqli_query($con,$sqlD)) {
				while ($row = mysqli_fetch_row($resultD))  {
					
					
					$extra_details[$idxR]['qta'] = $row[6];
					$extra_details[$idxR]['price'] = $row[7];
					$extra_details[$idxR]['extra_descr'] = $row[8];
					$extra_details[$idxR]['is_extra'] = true;

					$extra_details[$idxR]['note'] = $row[10];
					$extra_details[$idxR]['hours'] = $row[11];
					$extra_details[$idxR]['days'] = $row[12];
					
					
					$idxR  = $idxR  + 1;
				}
			}	
			$newArr[$idx]['extra_details'] = $extra_details;
			$idx = $idx + 1;
		}
		$con->close();
		echo json_encode($newArr);

	}
}


?>