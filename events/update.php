<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


// $postdata = file_get_contents("php://input");

if(!empty($_POST))
{
	// $request = json_decode($postdata);
  // var_dump($_POST);
	
	$oid = $_POST['oid'];
	$oid_business = json_decode($_POST['business'])->oid;
	$oid_customer = json_decode($_POST['customer'])->oid;
	$oid_event_type = json_decode($_POST['type'])->oid;
	$oid_event_status = json_decode($_POST['status'])->oid;
	$from = mysqli_real_escape_string($con, trim($_POST['from']));
	$to = mysqli_real_escape_string($con, trim($_POST['to']));
	$address = mysqli_real_escape_string($con, trim($_POST['address']));
	$info_event = mysqli_real_escape_string($con, trim($_POST['info_event']));
	$total = mysqli_real_escape_string($con, trim($_POST['total']));
	$total = str_replace(",", ".", $total);
	$total_real = mysqli_real_escape_string($con, trim($_POST['total_real']));
	$total_real = str_replace(",", ".", $total_real);
	$total_taxable = mysqli_real_escape_string($con, trim($_POST['total_taxable']));
	$total_taxable = str_replace(",", ".", $total_taxable);
	if (empty($total_taxable)) {
		$total_taxable = 0;
	}	
  $attach_file = mysqli_real_escape_string($con, trim($_POST['attach_file']));
	$vat = mysqli_real_escape_string($con, trim($_POST['vat']));
	if (empty($vat)) {
		$vat = 0;
	}
	$note = mysqli_real_escape_string($con, trim($_POST['note']));
	/*if ($request->package) {
		$oid_package = $request->package->oid;
	}
	else {
		$oid_package = 'NULL';
	}*/
	$total_days = mysqli_real_escape_string($con, trim($_POST['total_days']));
	$total_hours = mysqli_real_escape_string($con, trim($_POST['total_hours']));
	$oid_user_create = $currentUser['oid'];
	
	if ($_POST['all_day']) {
		$allDay = 1;
	}
	else {
		$allDay = 0;
	}

	if (!$total) {
		$total = 0;
	}
	if (!$total_real) {
		$total_real = 0;
	}
	
	
	
	
	$sql = "UPDATE `events` SET `oid_business` = {$oid_business}, `oid_customer` = {$oid_customer}, `oid_event_type` = {$oid_event_type}, `oid_event_status` = {$oid_event_status}, `from` = '{$from}', `to` = '{$to}',`address` = '{$address}', `info_event` = '{$info_event}', `total` = {$total},`total_real` = {$total_real}, `total_days` = {$total_days}, `total_hours`= {$total_hours}, `all_day`= {$allDay}, `note` = '{$note}', `vat` = {$vat}, `total_taxable` = {$total_taxable}, `attach_file` = '{$attach_file}' WHERE oid={$oid}";
	
  
	if(mysqli_query($con,$sql))
	{
		
		$sql = "DELETE FROM package_event_assoc where oid_event =  {$oid}";
		if (mysqli_query($con,$sql) && $_POST['packages_assoc']) {
			foreach (json_decode($_POST['packages_assoc']) as $value)
			{
			   
			   $sql = "SELECT max(oid)+1 as id from `package_event_assoc` ";
	
			    if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidPackageAssoc = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidPackageAssoc == NULL) {
					$oidPackageAssoc = 0;
				}
				
			   
			   $sql = "INSERT INTO `package_event_assoc` (`oid`,`oid_event`,`oid_package`) VALUES ({$oidPackageAssoc}, {$oid}, {$value->oid})";
			   if(mysqli_query($con,$sql))
			   {
				//OK
			   }
			   else {
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
			   }
			}
		}
		
		$sql = "DELETE FROM event_vendor_assoc where oid_event =  {$oid}";
		if (mysqli_query($con,$sql) && $_POST['vendor_assoc']) {
			foreach (json_decode($_POST['vendor_assoc']) as $value)
			{
			   
			   $sql = "SELECT max(oid)+1 as id from `event_vendor_assoc` ";
	
			    if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidVendorAssoc = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidVendorAssoc == NULL) {
					$oidVendorAssoc = 0;
				}
				
			   
			   $sql = "INSERT INTO `event_vendor_assoc` (`oid`,`oid_event`,`oid_vendor`) VALUES ({$oidVendorAssoc}, {$oid}, {$value->vendor->oid})";
			   if(mysqli_query($con,$sql))
			   {
				//OK
			   }
			   else {
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
			   }
			}
		}
		
		$sql = "DELETE FROM payments where oid_event =  {$oid}";
		if (mysqli_query($con,$sql) && $_POST['payment_assoc']) {
			foreach (json_decode($_POST['payment_assoc']) as $value)
			{
			   $amount = mysqli_real_escape_string($con, trim($value->amount));
         $paymentDate = mysqli_real_escape_string($con, trim($value->paymentDate));
         $paymentNote = mysqli_real_escape_string($con, trim($value->paymentNote));
			   if (substr_count($amount, ".") >=1 && substr_count($amount, ",") == 1) {
					$amount = str_replace(".", "", $amount);
					$amount = str_replace(",", ".", $amount);
				} else if (substr_count($amount, ".") ==0 && substr_count($amount, ",") == 1) {
					$amount = str_replace(",", ".", $amount);
				}
			   $sql = "SELECT max(oid)+1 as id from `payments` ";
	
			    if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidPaymentAssoc = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidPaymentAssoc == NULL) {
					$oidPaymentAssoc = 0;
				}
				
			   
			   $sql = "INSERT INTO `payments` (`oid`,`oid_event`,`amount`, `oid_payment_type`, `oid_payment_method`, `paymentDate`, `paymentNote`) VALUES ({$oidPaymentAssoc}, {$oid}, {$amount}, {$value->paymentType->oid}, {$value->paymentMethod->oid}, '{$paymentDate}', '{$paymentNote}')";
			   if(mysqli_query($con,$sql))
			   {
				//OK
			   }
			   else {
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
			   }
			}
		}
		
		$sql = "DELETE FROM event_detail where oid_event =  {$oid} and is_extra=0";
		if (mysqli_query($con,$sql) && $_POST['event_details']) { 
			
			foreach (json_decode($_POST['event_details']) as $value)
			{
				
				$price = mysqli_real_escape_string($con, trim($value->price));
				if (substr_count($price, ".") >=1 && substr_count($price, ",") == 1) {
					$price = str_replace(".", "", $price);
					$price = str_replace(",", ".", $price);
				} else if (substr_count($price, ".") ==0 && substr_count($price, ",") == 1) {
					$price = str_replace(",", ".", $price);
				}
				$total_price = mysqli_real_escape_string($con, trim($value->total_price));
				if (substr_count($total_price, ".") >=1 && substr_count($total_price, ",") == 1) {
					$total_price = str_replace(".", "", $total_price);
					$total_price = str_replace(",", ".", $total_price);
				} else if (substr_count($total_price, ".") ==0 && substr_count($total_price, ",") == 1) {
					$total_price = str_replace(",", ".", $total_price);
				}
				$sql = "SELECT max(oid)+1 as id from `event_detail` ";
				if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidEventDetail = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidEventDetail == NULL) {
					$oidEventDetail = 0;
				}
				if ($value->resourceType) {
					$oidResourceType = $value->resourceType->oid;
				}
				else {
					$oidResourceType = 'NULL';
				}
				if ($value->category) {
					$oidCategory = $value->category->oid;
				}
				else {
					$oidCategory = 'NULL';
				}
				if (isset($value->skill)) {
					$oidSkill = $value->skill->oid;
				}
				else {
					$oidSkill = 'NULL';
				}
				if (isset($value->resource)) {
					$oidResource = $value->resource->oid;
				}
				else {
					$oidResource = 'NULL';
				}
				if (isset($value->oid_package)) {
					$oidPackage = $value->oid_package;
				}
				else {
					$oidPackage = 'NULL';
				}
				
				
				$sql = "INSERT INTO `event_detail`(`oid`,`oid_event`,`oid_resource_type`,`oid_category`,`oid_skill`,`oid_resource`,`qta`,`price`,`extra_descr`,`is_extra`,`note`,`hours`,`days`,`total_price`,`oid_package`) VALUES ({$oidEventDetail},{$oid}, {$oidResourceType}, {$oidCategory}, {$oidSkill}, {$oidResource}, {$value->qta}, {$price},'', 0,'{$value->note}',{$value->hours}, {$value->days}, {$total_price}, {$oidPackage})";
				
				if(mysqli_query($con,$sql))
			    {
					if ($oidResource != 'NULL' && $oid_event_status == 5) {
						$sqlRes = "select * from resources where oid={$oidResource} and consumable=1 and oid_resource_type=2";
						if($resultRes = mysqli_query($con,$sqlRes)){
							if ($rowRes = mysqli_fetch_row($resultRes))  {
								$sqlBus = "select oid_business from role_user_assoc where oid_user= {$oid_user_create}";
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
												
												
												$oid_type_movement = 2; //USCITA
												$reason = "Evento n° ".$oid;
												$qta = $value->qta * -1;
												
												$sqlInsMag = "INSERT INTO `warehouse_movements` (`oid`, `oid_event`, `oid_warehouse`, `oid_user_operation`, `oid_resource`, `oid_type_movement`, `qta`, `reason`, `date_ts`) VALUES ({$oidMov}, {$oid}, {$oidMag}, {$oid_user_create}, {$oidResource}, {$oid_type_movement}, {$qta}, '{$reason}', now())";
												if(mysqli_query($con,$sqlInsMag))
												{
													
													
													$sqlUpdRes = "UPDATE resources set avail_qta = avail_qta + ({$qta}) where oid = {$oidResource}";
													if(mysqli_query($con,$sqlUpdRes)) 
													{
														//UPDATE RESOURCE OK
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
						}
					}
			    }
			    else {
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
			    }
			}
		}
		
		$sql = "DELETE FROM event_detail where oid_event =  {$oid} and is_extra=1";
		if (mysqli_query($con,$sql) && $_POST['extra_details']) {
			foreach (json_decode($_POST['extra_details']) as $value)
			{
				$price = mysqli_real_escape_string($con, trim($value->price));
				if (substr_count($price, ".") >=1 && substr_count($price, ",") == 1) {
					$price = str_replace(".", "", $price);
					$price = str_replace(",", ".", $price);
				} else if (substr_count($price, ".") ==0 && substr_count($price, ",") == 1) {
					$price = str_replace(",", ".", $price);
				}
				$total_price = mysqli_real_escape_string($con, trim($value->total_price));
				if (substr_count($total_price, ".") >=1 && substr_count($total_price, ",") == 1) {
					$total_price = str_replace(".", "", $total_price);
					$total_price = str_replace(",", ".", $total_price);
				} else if (substr_count($total_price, ".") ==0 && substr_count($total_price, ",") == 1) {
					$total_price = str_replace(",", ".", $total_price);
				}
				$sql = "SELECT max(oid)+1 as id from `event_detail` ";
				if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidEventDetail = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidEventDetail == NULL) {
					$oidEventDetail = 0;
				}
				$extra_descr = strtolower(trim($value->extra_descr));
				if (isset($value->oid_package)) {
					$oidPackage = $value->oid_package;
				}
				else {
					$oidPackage = 'NULL';
				}
				
				$sql = "INSERT INTO `event_detail` (`oid`,`oid_event`,`oid_resource_type`,`oid_category`,`oid_skill`,`oid_resource`,`qta`,`price`,`extra_descr`,`is_extra`,`note`,`hours`,`days`,`total_price`, `oid_package`) VALUES ({$oidEventDetail}, {$oid}, NULL, NULL, NULL, NULL, {$value->qta}, {$price},'{$extra_descr}', 1,'{$value->note}',NULL, NULL, {$total_price}, {$oidPackage})";

				if(mysqli_query($con,$sql))
			    {
					$sqlE = "SELECT 1 from `event_extras` where  extra_descr='{$extra_descr}' and oid_user in (select oid_user from role_user_assoc where oid_user={$oid_user_create} and oid_role in (select oid from roles where acronym in ('MANAGER','EDITOR')))";
					if($resultE = mysqli_query($con,$sqlE)) {
						if ($row = mysqli_fetch_row($resultE))  {
							//EXTRA GIA' ESISTE
						}
						else { //INSERISCI EXTRA
							$sql = "SELECT max(oid)+1 as id from `event_extras` ";
							if($result = mysqli_query($con,$sql)) {
								if ($row = mysqli_fetch_row($result))  {
									$oidExtra = $row[0];
								}
								else {
									http_response_code(500);
								}
							}
							if ($oidExtra == NULL) {
								$oidExtra = 0;
							}
							$sqlInsEx = "INSERT INTO event_extras (oid, extra_descr, oid_user) VALUES ({$oidExtra}, '{$extra_descr}', {$oid_user_create})";
							if(mysqli_query($con,$sqlInsEx))
							{
								
							}
							else {
								echo("Error description: " . mysqli_error($con));
								http_response_code(422);
							}
						}
					}
					//array_push($request->event_details, $value);
			    }
			    else {
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
			    }
			}
		}
		
		
		$sql = "DELETE FROM event_attribute where oid_event={$oid}";
		if (mysqli_query($con,$sql) && $_POST['attribute_assoc']) {
			foreach (json_decode($_POST['attribute_assoc']) as $value)
			{
				if ($value->value == '') {
					continue;
				}
			   
			    $sql = "SELECT max(oid)+1 as id from `event_attribute` ";
	
			    if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidAttributeAssoc = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidAttributeAssoc == NULL) {
					$oidAttributeAssoc = 0;
				}
				
				$oidAttribute = 'NULL';
				if (isset($value->oid)) {
					$oidAttribute = $value->oid;
				}
				
				
				
			   
			    $sql = "INSERT INTO `event_attribute` (`oid`,`oid_event`,`oid_attribute`,`name`,`value`) VALUES ({$oidAttributeAssoc}, {$oid}, {$oidAttribute},'{$value->name}', '{$value->value}')";
			    if(mysqli_query($con,$sql))
			    {
				 //OK
			    }
			    else {
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
			    }
			}
		}

    if (!empty($_FILES["attch_file"]["name"])) {
			// var_dump($_FILES["attch_file"]["name"]);
			$userdir = $locationEvent.$oid;
		
			if(!file_exists($userdir)){
				mkdir($userdir, 0755, true );
			}
			$filename = $_FILES['attch_file']['name'];
			$userdir .= "/".$filename;
			$array = explode('.', $_FILES['attch_file']['name']);
			$extension = end($array);
		
			if (($_FILES["attch_file"]["type"] == "*") || ($_FILES["attch_file"]["size"] < 10485760)) {
				 move_uploaded_file($_FILES['attch_file']['tmp_name'],$userdir);
			}
			else {
				echo("Error description: File not allowed");
				http_response_code(500);
				exit;
			}
		}
		
		
		$con->close();;
		echo json_encode($_POST);
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