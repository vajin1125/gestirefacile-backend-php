<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

$oid_user = $currentUser['oid'];

if(isset($_GET['oid'])) {
  $oid = $_GET['oid'];
  // echo $oid;
  $sql = "SELECT * FROM events where oid='{$oid}' and oid_business in (select oid_business from role_user_assoc where oid_user={$oid_user} and oid_role in (select oid from roles where acronym in ('MANAGER','EDITOR')))";

	if($result = mysqli_query($con,$sql))
	{
		$idx = 0;
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
			$oid_business = $db_field['oid_business'];
			$oid_customer = $db_field['oid_customer'];
			$oid_event_type = $db_field['oid_event_type'];
			$oid_event_status = $db_field['oid_event_status'];
			
			$sqlB = "select * from business where oid={$oid_business}";
			if($resultB = mysqli_query($con,$sqlB))
			{
				if ($db_field_b = mysqli_fetch_assoc($resultB))  {
					$newArr[$idx]['business'] = $db_field_b;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlC = "select * from customers where oid={$oid_customer}";
			if($resultC = mysqli_query($con,$sqlC))
			{
				if ($db_field_c = mysqli_fetch_assoc($resultC))  {
					$newArr[$idx]['customer'] = $db_field_c;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlT = "select * from event_type where oid={$oid_event_type}";
			if($resultT = mysqli_query($con,$sqlT))
			{
				if ($db_field_t = mysqli_fetch_assoc($resultT))  {
					$newArr[$idx]['type'] = $db_field_t;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlS = "select * from event_status where oid={$oid_event_status}";
			if($resultS = mysqli_query($con,$sqlS))
			{
				if ($db_field_s = mysqli_fetch_assoc($resultS))  {
					$newArr[$idx]['status'] = $db_field_s;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlV = "select oid, oid_vendor from event_vendor_assoc where oid_event={$oid}";
			$vendor_assoc = array();
			$idxV = 0;
			if($resultV = mysqli_query($con,$sqlV)) {
				while ($row = mysqli_fetch_row($resultV))  {
					$oidAssoc = $row[0];
					$oidVendorAssoc = $row[1];
		
					
					$sqlVendor = "SELECT * FROM vendors where oid={$oidVendorAssoc}";
					
					if($resultVendor = mysqli_query($con,$sqlVendor)) {
						if ($db_field_vendor = mysqli_fetch_assoc($resultVendor))  {
							$vendor_assoc[$idxV]['vendor'] = $db_field_vendor;
						}
						else {
							http_response_code(500);
						}
					}
					$idxV  = $idxV  + 1;
				}
			}	
			$newArr[$idx]['vendor_assoc'] = $vendor_assoc;
			
			
			$sqlP = "select oid, oid_payment_type, oid_payment_method, amount, paymentDate, paymentNote from payments where oid_event={$oid}";
			$payment_assoc = array();
			$idxP = 0;
			if($resultP = mysqli_query($con,$sqlP)) {
				while ($row = mysqli_fetch_row($resultP))  {
					$oidAssoc = $row[0];
					$oidPayTypeAssoc = $row[1];
					$oidPayMetAssoc = $row[2];
					$amount = $row[3];
					$paymentDate = $row[4];
					$paymentNote = $row[5];
					
					$sqlPT = "SELECT * FROM payment_types where oid={$oidPayTypeAssoc}";
					
					if($resultPT = mysqli_query($con,$sqlPT)) {
						if ($db_field_pt = mysqli_fetch_assoc($resultPT))  {
							$payment_assoc[$idxP]['paymentType'] = $db_field_pt;
						}
						else {
							http_response_code(500);
						}
					}
					
					$sqlPM = "SELECT * FROM payment_methods where oid={$oidPayMetAssoc}";
					
					if($resultPM = mysqli_query($con,$sqlPM)) {
						if ($db_field_pm = mysqli_fetch_assoc($resultPM))  {
							$payment_assoc[$idxP]['paymentMethod'] = $db_field_pm;
						}
						else {
							http_response_code(500);
						}
					}
					$payment_assoc[$idxP]['amount'] = $amount;
					$payment_assoc[$idxP]['paymentDate'] = $paymentDate;
					$payment_assoc[$idxP]['paymentNote'] = $paymentNote;
          
					$idxP  = $idxP  + 1;
				}
			}	
			$newArr[$idx]['payment_assoc'] = $payment_assoc;
			
			
			$sqlD = "select `oid`, `oid_event`, `oid_resource_type`, `oid_category`, `oid_skill`, `oid_resource`, `qta`, `price`, `extra_descr`, `is_extra`, `note`, `hours`, `days`, `total_price`, `oid_package`  from event_detail where oid_event={$oid} and is_extra=0";
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
					$event_details[$idxR]['oid_package'] = $row[14];
					
					$idxR  = $idxR  + 1;
				}
			}	
			$newArr[$idx]['event_details'] = $event_details;
			
			
			$sqlD = "select `oid`, `oid_event`, `oid_resource_type`, `oid_category`, `oid_skill`, `oid_resource`, `qta`, `price`, `extra_descr`, `is_extra`, `note`, `hours`, `days`, `total_price`, `oid_package`  from event_detail where oid_event={$oid} and is_extra=1";
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
					$extra_details[$idxR]['oid_package'] = $row[14];
					
					$idxR  = $idxR  + 1;
				}
			}	
			$newArr[$idx]['extra_details'] = $extra_details;
			
			$sqlA = "SELECT * FROM event_attribute where oid_event={$oid}";
			$attributes = array();
			
			if($resultA = mysqli_query($con,$sqlA)) {
				while ($rowA = mysqli_fetch_assoc($resultA))  {
					$attributes[] = $rowA;
				}
			}
			$newArr[$idx]['attribute_assoc'] = $attributes;
			
			$sqlPkg = "SELECT p.* FROM packages p, package_event_assoc pa where pa.oid_event={$oid} and pa.oid_package = p.oid";
			$packages = array();
			
			if($resultPkg = mysqli_query($con,$sqlPkg)) {
				$i = 0;
				while ($rowPkg = mysqli_fetch_assoc($resultPkg))  {
					//$packages[$i]['package'] = $rowPkg;
					$packages[] = $rowPkg;
					$i  = $i  + 1;
				}
			}
			$newArr[$idx]['packages_assoc'] = $packages;
			
			
			$idx  = $idx  + 1;
		}
		$con->close();
		echo json_encode($newArr);

	}
} elseif(isset($_GET['oid_customer'])) {
  $oid_customer = $_GET['oid_customer'];
  // echo $oid;
  $sql = "SELECT * FROM events where oid_customer='{$oid_customer}' and oid_user_create='{$oid_user}' and oid_business in (select oid_business from role_user_assoc where oid_user={$oid_user} and oid_role in (select oid from roles where acronym in ('MANAGER','EDITOR')))";

	if($result = mysqli_query($con,$sql))
	{
		$idx = 0;
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
      $oid = $db_field['oid'];
			$oid_business = $db_field['oid_business'];
			$oid_customer = $db_field['oid_customer'];
			$oid_event_type = $db_field['oid_event_type'];
			$oid_event_status = $db_field['oid_event_status'];
			
			$sqlB = "select * from business where oid={$oid_business}";
			if($resultB = mysqli_query($con,$sqlB))
			{
				if ($db_field_b = mysqli_fetch_assoc($resultB))  {
					$newArr[$idx]['business'] = $db_field_b;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlC = "select * from customers where oid={$oid_customer}";
			if($resultC = mysqli_query($con,$sqlC))
			{
				if ($db_field_c = mysqli_fetch_assoc($resultC))  {
					$newArr[$idx]['customer'] = $db_field_c;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlT = "select * from event_type where oid={$oid_event_type}";
			if($resultT = mysqli_query($con,$sqlT))
			{
				if ($db_field_t = mysqli_fetch_assoc($resultT))  {
					$newArr[$idx]['type'] = $db_field_t;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlS = "select * from event_status where oid={$oid_event_status}";
			if($resultS = mysqli_query($con,$sqlS))
			{
				if ($db_field_s = mysqli_fetch_assoc($resultS))  {
					$newArr[$idx]['status'] = $db_field_s;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlV = "select oid, oid_vendor from event_vendor_assoc where oid_event={$oid}";
			$vendor_assoc = array();
			$idxV = 0;
			if($resultV = mysqli_query($con,$sqlV)) {
				while ($row = mysqli_fetch_row($resultV))  {
					$oidAssoc = $row[0];
					$oidVendorAssoc = $row[1];
		
					
					$sqlVendor = "SELECT * FROM vendors where oid={$oidVendorAssoc}";
					
					if($resultVendor = mysqli_query($con,$sqlVendor)) {
						if ($db_field_vendor = mysqli_fetch_assoc($resultVendor))  {
							$vendor_assoc[$idxV]['vendor'] = $db_field_vendor;
						}
						else {
							http_response_code(500);
						}
					}
					$idxV  = $idxV  + 1;
				}
			}	
			$newArr[$idx]['vendor_assoc'] = $vendor_assoc;
			
			
			$sqlP = "select oid, oid_payment_type, oid_payment_method, amount, paymentDate, paymentNote from payments where oid_event={$oid}";
			$payment_assoc = array();
			$idxP = 0;
			if($resultP = mysqli_query($con,$sqlP)) {
				while ($row = mysqli_fetch_row($resultP))  {
					$oidAssoc = $row[0];
					$oidPayTypeAssoc = $row[1];
					$oidPayMetAssoc = $row[2];
					$amount = $row[3];
          $paymentDate = $row[4];
					$paymentNote = $row[5];
					
					$sqlPT = "SELECT * FROM payment_types where oid={$oidPayTypeAssoc}";
					
					if($resultPT = mysqli_query($con,$sqlPT)) {
						if ($db_field_pt = mysqli_fetch_assoc($resultPT))  {
							$payment_assoc[$idxP]['paymentType'] = $db_field_pt;
						}
						else {
							http_response_code(500);
						}
					}
					
					$sqlPM = "SELECT * FROM payment_methods where oid={$oidPayMetAssoc}";
					
					if($resultPM = mysqli_query($con,$sqlPM)) {
						if ($db_field_pm = mysqli_fetch_assoc($resultPM))  {
							$payment_assoc[$idxP]['paymentMethod'] = $db_field_pm;
						}
						else {
							http_response_code(500);
						}
					}
					$payment_assoc[$idxP]['amount'] = $amount;
          $payment_assoc[$idxP]['paymentDate'] = $paymentDate;
					$payment_assoc[$idxP]['paymentNote'] = $paymentNote;

					$idxP  = $idxP  + 1;
				}
			}	
			$newArr[$idx]['payment_assoc'] = $payment_assoc;
			
			
			$sqlD = "select `oid`, `oid_event`, `oid_resource_type`, `oid_category`, `oid_skill`, `oid_resource`, `qta`, `price`, `extra_descr`, `is_extra`, `note`, `hours`, `days`, `total_price`, `oid_package`  from event_detail where oid_event={$oid} and is_extra=0";
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
					$event_details[$idxR]['oid_package'] = $row[14];
					
					$idxR  = $idxR  + 1;
				}
			}	
			$newArr[$idx]['event_details'] = $event_details;
			
			
			$sqlD = "select `oid`, `oid_event`, `oid_resource_type`, `oid_category`, `oid_skill`, `oid_resource`, `qta`, `price`, `extra_descr`, `is_extra`, `note`, `hours`, `days`, `total_price`, `oid_package`  from event_detail where oid_event={$oid} and is_extra=1";
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
					$extra_details[$idxR]['oid_package'] = $row[14];
					
					$idxR  = $idxR  + 1;
				}
			}	
			$newArr[$idx]['extra_details'] = $extra_details;
			
			$sqlA = "SELECT * FROM event_attribute where oid_event={$oid}";
			$attributes = array();
			
			if($resultA = mysqli_query($con,$sqlA)) {
				while ($rowA = mysqli_fetch_assoc($resultA))  {
					$attributes[] = $rowA;
				}
			}
			$newArr[$idx]['attribute_assoc'] = $attributes;
			
			$sqlPkg = "SELECT p.* FROM packages p, package_event_assoc pa where pa.oid_event={$oid} and pa.oid_package = p.oid";
			$packages = array();
			
			if($resultPkg = mysqli_query($con,$sqlPkg)) {
				$i = 0;
				while ($rowPkg = mysqli_fetch_assoc($resultPkg))  {
					//$packages[$i]['package'] = $rowPkg;
					$packages[] = $rowPkg;
					$i  = $i  + 1;
				}
			}
			$newArr[$idx]['packages_assoc'] = $packages;
			
			
			$idx  = $idx  + 1;
		}
		$con->close();
		echo json_encode($newArr);

	}
}
else {
    if (isset($_GET['trashed'])) {
        $sql = "SELECT * FROM events e where is_trash=1 and oid_business in (select oid_business from role_user_assoc where oid_user={$oid_user} and oid_role in (select oid from roles where acronym in ('MANAGER','EDITOR'))) order by  e.from DESC";
    } else {
        $sql = "SELECT * FROM events e where is_trash=0 and oid_business in (select oid_business from role_user_assoc where oid_user={$oid_user} and oid_role in (select oid from roles where acronym in ('MANAGER','EDITOR'))) order by  e.from DESC";
    }

	if($result = mysqli_query($con,$sql))
	{
		$idx = 0;
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$warning = false;
			$newArr[] = $db_field;
			$start = $db_field['from'];
			$end = $db_field['to'];
			$oid_business = $db_field['oid_business'];
			$oid_customer = $db_field['oid_customer'];
			$oid_event_type = $db_field['oid_event_type'];
			$oid_event_status = $db_field['oid_event_status'];
			$oid = $db_field['oid'];
			
			$sqlB = "select * from business where oid={$oid_business}";
			if($resultB = mysqli_query($con,$sqlB))
			{
				if ($db_field_b = mysqli_fetch_assoc($resultB))  {
					$newArr[$idx]['business'] = $db_field_b;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlC = "select * from customers where oid={$oid_customer}";
			if($resultC = mysqli_query($con,$sqlC))
			{
				if ($db_field_c = mysqli_fetch_assoc($resultC))  {
					$newArr[$idx]['customer'] = $db_field_c;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlT = "select * from event_type where oid={$oid_event_type}";
			if($resultT = mysqli_query($con,$sqlT))
			{
				if ($db_field_t = mysqli_fetch_assoc($resultT))  {
					$newArr[$idx]['type'] = $db_field_t;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlS = "select * from event_status where oid={$oid_event_status}";
			if($resultS = mysqli_query($con,$sqlS))
			{
				if ($db_field_s = mysqli_fetch_assoc($resultS))  {
					$newArr[$idx]['status'] = $db_field_s;
				}
				else {
					http_response_code(500);
				}
			}
			
			$sqlV = "select oid, oid_vendor from event_vendor_assoc where oid_event={$oid}";
			$vendor_assoc = array();
			$idxV = 0;
			if($resultV = mysqli_query($con,$sqlV)) {
				while ($row = mysqli_fetch_row($resultV))  {
					$oidAssoc = $row[0];
					$oidVendorAssoc = $row[1];
		
					
					$sqlVendor = "SELECT * FROM vendors where oid={$oidVendorAssoc}";
					
					if($resultVendor = mysqli_query($con,$sqlVendor)) {
						if ($db_field_vendor = mysqli_fetch_assoc($resultVendor))  {
							$vendor_assoc[$idxV]['vendor'] = $db_field_vendor;
						}
						else {
							http_response_code(500);
						}
					}
					$idxV  = $idxV  + 1;
				}
			}	
			$newArr[$idx]['vendor_assoc'] = $vendor_assoc;
			
			
			$sqlP = "select oid, oid_payment_type, oid_payment_method, amount, paymentDate, paymentNote from payments where oid_event={$oid}";
			$payment_assoc = array();
			$idxP = 0;
			if($resultP = mysqli_query($con,$sqlP)) {
				while ($row = mysqli_fetch_row($resultP))  {
					$oidAssoc = $row[0];
					$oidPayTypeAssoc = $row[1];
					$oidPayMetAssoc = $row[2];
					$amount = $row[3];
          $paymentDate = $row[4];
					$paymentNote = $row[5];
					
					$sqlPT = "SELECT * FROM payment_types where oid={$oidPayTypeAssoc}";
					
					if($resultPT = mysqli_query($con,$sqlPT)) {
						if ($db_field_pt = mysqli_fetch_assoc($resultPT))  {
							$payment_assoc[$idxP]['paymentType'] = $db_field_pt;
						}
						else {
							http_response_code(500);
						}
					}
					
					$sqlPM = "SELECT * FROM payment_methods where oid={$oidPayMetAssoc}";
					
					if($resultPM = mysqli_query($con,$sqlPM)) {
						if ($db_field_pm = mysqli_fetch_assoc($resultPM))  {
							$payment_assoc[$idxP]['paymentMethod'] = $db_field_pm;
						}
						else {
							http_response_code(500);
						}
					}
					$payment_assoc[$idxP]['amount'] = $amount;
          $payment_assoc[$idxP]['paymentDate'] = $paymentDate;
					$payment_assoc[$idxP]['paymentNote'] = $paymentNote;
          
					$idxP  = $idxP  + 1;
				}
			}	
			$newArr[$idx]['payment_assoc'] = $payment_assoc;
			
			
			$sqlD = "select `oid`, `oid_event`, `oid_resource_type`, `oid_category`, `oid_skill`, `oid_resource`, `qta`, `price`, `extra_descr`, `is_extra`, `note`, `hours`, `days`, `total_price`, `oid_package`from event_detail where oid_event={$oid} and is_extra=0";
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
					$warning = false;
					if ($oidResource) {
						$sqlR = "SELECT * FROM resources where oid={$oidResource}";
					
						if($resultR = mysqli_query($con,$sqlR)) {
							if ($db_field_r = mysqli_fetch_assoc($resultR))  {
								$event_details[$idxR]['resource'] = $db_field_r;
								$avail_qta = $db_field_r['avail_qta'];
								$sqlAv = "SELECT sum(ed.qta) as qta FROM events e, event_detail ed where e.oid = ed.oid_event  and e.oid_business in (select oid_business from role_user_assoc where oid_user={$oid_user}) and ed.oid_resource={$oidResource} and CAST('{$start}' AS DATETIME) <= e.to and CAST('{$end}' AS DATETIME) >= e.from and e.oid_event_status in (2,3)";
								if($resultAv = mysqli_query($con,$sqlAv)) {
									if ($db_field_av = mysqli_fetch_assoc($resultAv))  {
										$sumqta = $db_field_av['qta'];
										if ($sumqta > $avail_qta) {
											$warning = true;
										}
									}
								}
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
					$event_details[$idxR]['oid_package'] = $row[14];
					
					$idxR  = $idxR  + 1;
				}
			}	
			$newArr[$idx]['event_details'] = $event_details;
			
			
			$sqlD = "select `oid`, `oid_event`, `oid_resource_type`, `oid_category`, `oid_skill`, `oid_resource`, `qta`, `price`, `extra_descr`, `is_extra`, `note`, `hours`, `days`, `total_price`, `oid_package` from event_detail where oid_event={$oid} and is_extra=1";
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
					$extra_details[$idxR]['oid_package'] = $row[14];
					
					$idxR  = $idxR  + 1;
				}
			}	
			$newArr[$idx]['extra_details'] = $extra_details;
			
			$sqlA = "SELECT * FROM event_attribute where oid_event={$oid}";
			$attributes = array();
			
			if($resultA = mysqli_query($con,$sqlA)) {
				while ($rowA = mysqli_fetch_assoc($resultA))  {
					$attributes[] = $rowA;
				}
			}
			$newArr[$idx]['attribute_assoc'] = $attributes;
			
			$sqlPkg = "SELECT p.* FROM packages p, package_event_assoc pa where pa.oid_event={$oid} and pa.oid_package = p.oid";
			$packages = array();
			
			if($resultPkg = mysqli_query($con,$sqlPkg)) {
				$i = 0;
				while ($rowPkg = mysqli_fetch_assoc($resultPkg))  {
					//$packages[$i]['package'] = $rowPkg;
					$packages[] = $rowPkg;
					$i  = $i  + 1;
				}
			}
			$newArr[$idx]['packages_assoc'] = $packages;
			
			
			
			
			$newArr[$idx]['warning'] = $warning;
			$idx  = $idx  + 1;
		}
		
		
		
		$con->close();
		echo json_encode($newArr);

	}
}


?>