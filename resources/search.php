<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

$oid_user = $currentUser['oid'];

if(isset($_GET['oid'])) {
	$oid = $_GET['oid'];
	$sql = "SELECT * FROM resources where oid='{$oid}' and oid_user={$oid_user}";

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
		}
		
		
		
		$sql = "SELECT rt.oid, rt.acronym, rt.descr FROM resource_types rt, resources r where r.oid='{$oid}' and r.oid_user={$oid_user} and r.oid_resource_type = rt.oid";

		if($result = mysqli_query($con,$sql)) {
			if ($db_field = mysqli_fetch_assoc($result))  {
				$newArr[0]['resourceType'] = $db_field;
			}
		}
		
		$sql = "SELECT u.* FROM users u, resources r where r.oid='{$oid}' and r.oid_user={$oid_user} and r.oid_user_res = u.oid";

		if($result = mysqli_query($con,$sql)) {
			if ($db_field = mysqli_fetch_assoc($result))  {
				$newArr[0]['user_resource'] = $db_field;
			}
		}
		

		$sql = "SELECT oid, oid_resource_assoc, qta, address, refNumber FROM resource_resources_assoc where oid_resource='{$oid}'";
		$resources_assoc = array();
		$idx = 0;
		if($result = mysqli_query($con,$sql)) {
			while ($row = mysqli_fetch_row($result))  {
				$oidAssoc = $row[0];
				$oidResourceAssoc = $row[1];
				$qta = $row[2];
        $address = $row[3];
        $refNumber = $row[4];
				
				$sqlb = "SELECT * FROM resources where oid='{$oidResourceAssoc}'";
				
				if($resultB = mysqli_query($con,$sqlb)) {
					if ($db_field_b = mysqli_fetch_assoc($resultB))  {
						$resources_assoc[$idx]['oid'] = $oidAssoc;
						$resources_assoc[$idx]['resourceAssoc'] = $db_field_b;
						$resources_assoc[$idx]['qta'] = $qta;
            $resources_assoc[$idx]['address'] = $address;
            $resources_assoc[$idx]['refNumber'] = $refNumber;
					}
					else {
						http_response_code(500);
					}
			    }
				$idx  = $idx  + 1;
			}
		}
		$newArr[0]['resources_assoc'] = $resources_assoc;
		
		
		$sql = "SELECT oid, oid_category FROM category_resource_assoc where oid_resource='{$oid}'";
		$categories_assoc = array();
		$idx = 0;
		if($result = mysqli_query($con,$sql)) {
			while ($row = mysqli_fetch_row($result))  {
				$oidAssoc = $row[0];
				$oidCategory = $row[1];

				
				$sqlb = "SELECT * FROM categories where oid='{$oidCategory}'";
				
				if($resultB = mysqli_query($con,$sqlb)) {
					if ($db_field_b = mysqli_fetch_assoc($resultB))  {
						$categories_assoc[$idx] = $db_field_b;
					}
					else {
						http_response_code(500);
					}
			    }
				$idx  = $idx  + 1;
			}
		}
		$newArr[0]['categories_assoc'] = $categories_assoc;
		
		
		$sql = "SELECT oid, oid_skill FROM resource_skill_assoc where oid_resource='{$oid}'";
		$skills_assoc = array();
		$idx = 0;
		if($result = mysqli_query($con,$sql)) {
			while ($row = mysqli_fetch_row($result))  {
				$oidAssoc = $row[0];
				$oidSkill = $row[1];

				
				$sqlb = "SELECT * FROM skills where oid='{$oidSkill}'";
				
				if($resultB = mysqli_query($con,$sqlb)) {
					if ($db_field_b = mysqli_fetch_assoc($resultB))  {
						$skills_assoc[$idx] = $db_field_b;
					}
					else {
						http_response_code(500);
					}
			    }
				$idx  = $idx  + 1;
			}
		}
		$newArr[0]['skills_assoc'] = $skills_assoc;
		
		
		$sqlM = "SELECT * FROM warehouse_movements where oid_resource='{$oid}' order by oid desc";
		$movements = array();
		$idx = 0;
		if($resultM = mysqli_query($con,$sqlM)) {
			while ($db_field_m = mysqli_fetch_assoc($resultM))  {
				$movements[$idx] = $db_field_m;
				$idx  = $idx  + 1;
			}
		}
		$newArr[0]['movements'] = $movements;
		
		
		$sqlP = "SELECT * FROM prices where oid_resource='{$oid}'";
		$prices = array();
		$idx = 0;
		if($resultP = mysqli_query($con,$sqlP)) {
			while ($db_field_p = mysqli_fetch_assoc($resultP))  {
				$prices[$idx] = $db_field_p;
				$idx  = $idx  + 1;
			}
		}
		$newArr[0]['prices'] = $prices;

    $sqlEvent = "
        SELECT *, customers.name as clientName, customers.surname as clientSurname, customers.address as clientAddress, events.address as eventAddress
        FROM event_detail 
        LEFT JOIN events ON event_detail.oid_event=events.oid 
        LEFT JOIN customers ON events.oid_customer=customers.oid 
        WHERE event_detail.oid_resource='{$oid}'
        ";
        $events = array();
        $idx_event = 0;
        if($resultEvent = mysqli_query($con, $sqlEvent)) {
            while($db_field_Event = mysqli_fetch_assoc($resultEvent)) {
            $events[$idx_event] = $db_field_Event;
            $idx_event = $idx_event + 1;
            }
        }
        $newArr[0]['events'] = $events;
		
		
		$con->close();
		echo json_encode($newArr);

	}
}
else {
    if (isset($_GET['trashed'])) {
        $sql = "SELECT * FROM resources where oid_user={$oid_user} and is_trash=1 order by oid desc";
    } else {
        $sql = "SELECT * FROM resources where oid_user={$oid_user} and is_trash=0 order by oid desc";
    }

	if($result = mysqli_query($con,$sql))
	{
		$newArr = array();
		$idx = 0;
		while ($db_field = mysqli_fetch_assoc($result)) {
			$newArr[] = $db_field;
			$oidResource = $db_field["oid"];
			$sqlT = "SELECT rt.oid, rt.acronym, rt.descr FROM resource_types rt, resources r where r.oid='{$oidResource }' and r.oid_user={$oid_user} and r.oid_resource_type = rt.oid";

			if($resultT = mysqli_query($con,$sqlT)) {
				if ($db_fieldT = mysqli_fetch_assoc($resultT))  {
					$newArr[$idx]['resourceType'] = $db_fieldT;
				}
			}

			$sqlR = "SELECT oid, oid_resource_assoc, qta, address, refNumber FROM resource_resources_assoc where oid_resource='{$oidResource}'";
			$resources_assoc = array();
			$idxR = 0;
			if($resultR = mysqli_query($con,$sqlR)) {
				while ($row = mysqli_fetch_row($resultR))  {
					$oidAssoc = $row[0];
					$oidResourceAssoc = $row[1];
					$qta = $row[2];
          $address = $row[3];
          $refNumber = $row[4];
					
					$sqlb = "SELECT * FROM resources where oid='{$oidResourceAssoc}'";
					
					if($resultB = mysqli_query($con,$sqlb)) {
						if ($db_field_b = mysqli_fetch_assoc($resultB))  {
							$resources_assoc[$idxR]['oid'] = $oidAssoc;
							$resources_assoc[$idxR]['resourceAssoc'] = $db_field_b;
							$resources_assoc[$idxR]['qta'] = $qta;
              $resources_assoc[$idx]['address'] = $address;
              $resources_assoc[$idx]['refNumber'] = $refNumber;
						}
						else {
							http_response_code(500);
						}
					}
					$idxR  = $idxR  + 1;
				}
			}
			$newArr[$idx]['resources_assoc'] = $resources_assoc;
			
			
			$sqlC = "SELECT oid, oid_category FROM category_resource_assoc where oid_resource='{$oidResource}'";
			$categories_assoc = array();
			$idxC = 0;
			if($resultC = mysqli_query($con,$sqlC)) {
				while ($row = mysqli_fetch_row($resultC))  {
					$oidAssoc = $row[0];
					$oidCategory = $row[1];

					
					$sqlb = "SELECT * FROM categories where oid='{$oidCategory}'";
					
					if($resultB = mysqli_query($con,$sqlb)) {
						if ($db_field_b = mysqli_fetch_assoc($resultB))  {
							$categories_assoc[$idxC] = $db_field_b;
						}
						else {
							http_response_code(500);
						}
					}
					$idxC  = $idxC  + 1;
				}
			}
			$newArr[$idx]['categories_assoc'] = $categories_assoc;
			
			
			$sqlS = "SELECT oid, oid_skill FROM resource_skill_assoc where oid_resource='{$oidResource}'";
			$skills_assoc = array();
			$idxS = 0;
			if($resultS = mysqli_query($con,$sqlS)) {
				while ($row = mysqli_fetch_row($resultS))  {
					$oidAssoc = $row[0];
					$oidSkill = $row[1];

					
					$sqlb = "SELECT * FROM skills where oid='{$oidSkill}'";
					
					if($resultB = mysqli_query($con,$sqlb)) {
						if ($db_field_b = mysqli_fetch_assoc($resultB))  {
							$skills_assoc[$idxS] = $db_field_b;
						}
						else {
							http_response_code(500);
						}
					}
					$idxS  = $idxS  + 1;
				}
			}
			$newArr[$idx]['skills_assoc'] = $skills_assoc;
			
			
			$sqlP = "SELECT * FROM prices where oid_resource='{$oidResource}'";
			$prices = array();
			$idxP = 0;
			if($resultP = mysqli_query($con,$sqlP)) {
				while ($db_field_p = mysqli_fetch_assoc($resultP))  {
					$prices[$idxP] = $db_field_p;
					$idxP  = $idxP  + 1;
				}
			}
			$newArr[$idx]['prices'] = $prices;

      // get event count and event list
      // $sqlEvent = "SELECT *, customers.name as clientName, customers.address as clientAddress, event_detail.address as eventAddress, events.from as eventFrom, events.to as eventTo FROM event_detail 
      //   LEFT JOIN events ON event_detail.oid_event=events.oid 
      //   LEFT JOIN customers ON events.oid_customer=customers.oid 
      //   where event_detail.oid_resource='{$oidResource}'";
      $sqlEvent = "
      SELECT *, customers.name as clientName, customers.surname as clientSurname, customers.address as clientAddress, events.address as eventAddress
      FROM event_detail 
      LEFT JOIN events ON event_detail.oid_event=events.oid 
      LEFT JOIN customers ON events.oid_customer=customers.oid 
      WHERE event_detail.oid_resource='{$oidResource}'
      ";
      $events = array();
      $idx_event = 0;
      if($resultEvent = mysqli_query($con, $sqlEvent)) {
        while($db_field_Event = mysqli_fetch_assoc($resultEvent)) {
          $events[$idx_event] = $db_field_Event;
          $idx_event = $idx_event + 1;
        }
      }
      $newArr[$idx]['events'] = $events;



			$idx  = $idx  + 1;
		}
		$con->close();
		echo json_encode($newArr);

	}
}


?>