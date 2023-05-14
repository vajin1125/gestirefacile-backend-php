<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


// $postdata = file_get_contents("php://input");

if (!empty($_POST)) 
{
	// $request = json_decode($postdata);
	$oid_user = $currentUser['oid'];
	$oid = mysqli_real_escape_string($con, trim($_POST['oid']));
	$name = mysqli_real_escape_string($con, trim($_POST['name']));
	$descr = mysqli_real_escape_string($con, trim($_POST['descr']));
	if ($_POST['enabled'] == 'true') {
		$enabled = 1;
	}
	else {
		$enabled = 0;
	}
	$total_price = mysqli_real_escape_string($con, trim($_POST['total_price']));
	$total_price = str_replace(",", ".", $total_price);

  if($_POST['image'] != '') {
    $image = mysqli_real_escape_string($con, trim($_POST['image']));
  } else {
    $image = '';
  }
	
	$sql = "UPDATE `packages` SET `name` = '{$name}', `descr` = '{$descr}', `enabled` = {$enabled}, `total_price` = {$total_price}, `image` = '{$image}' WHERE `packages`.`oid` = {$oid}";
	
	
	if(mysqli_query($con,$sql))
	{
		$sql = "DELETE FROM package_detail where oid_package =  {$oid} and is_extra=0";
    $packageDetails = json_decode($_POST['package_details']);
		if (mysqli_query($con,$sql) && $packageDetails) {
			foreach ($packageDetails as $value)
			{
				
				$price = mysqli_real_escape_string($con, trim($value->price));
				if (substr_count($price, ".") >=1 && substr_count($price, ",") == 1) {
					$price = str_replace(".", "", $price);
					$price = str_replace(",", ".", $price);
				} else if (substr_count($price, ".") ==0 && substr_count($price, ",") == 1) {
					$price = str_replace(",", ".", $price);
				}
				$sql = "SELECT max(oid)+1 as id from `package_detail` ";
				if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidPackageDetail = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidPackageDetail == NULL) {
					$oidPackageDetail = 0;
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
				
				
				$sql = "INSERT INTO `package_detail`(`oid`,`oid_package`,`oid_resource_type`,`oid_category`,`oid_skill`,`oid_resource`,`qta`,`price`,`extra_descr`,`is_extra`,`note`,`hours`,`days`,`total_price`) VALUES ({$oidPackageDetail},{$oid}, {$oidResourceType}, {$oidCategory}, {$oidSkill}, {$oidResource}, {$value->qta}, {$price},'', 0,'{$value->note}',{$value->hours}, {$value->days}, {$value->total_price})";
				
				if(mysqli_query($con,$sql))
			    {
					// INSERT OK
			    }
			    else {
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
			    }
			}
		}
		
		$sql = "DELETE FROM package_detail where oid_package =  {$oid} and is_extra=1";
    $packageDetails = json_decode($_POST['package_details']);
		if (mysqli_query($con,$sql) && $packageDetails) {
			foreach ($packageDetails as $value)
			{
				$price = mysqli_real_escape_string($con, trim($value->price));
				if (substr_count($price, ".") >=1 && substr_count($price, ",") == 1) {
					$price = str_replace(".", "", $price);
					$price = str_replace(",", ".", $price);
				} else if (substr_count($price, ".") ==0 && substr_count($price, ",") == 1) {
					$price = str_replace(",", ".", $price);
				}
				$sql = "SELECT max(oid)+1 as id from `package_detail` ";
				if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidPackageDetail = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidPackageDetail == NULL) {
					$oidPackageDetail = 0;
				}
				$extra_descr = strtolower(trim($value->extra_descr));
				$sql = "INSERT INTO `package_detail` (`oid`,`oid_package`,`oid_resource_type`,`oid_category`,`oid_skill`,`oid_resource`,`qta`,`price`,`extra_descr`,`is_extra`,`note`,`hours`,`days`,`total_price`) VALUES ({$oidPackageDetail}, {$oid}, NULL, NULL, NULL, NULL, {$value->qta}, {$price},'{$extra_descr}', 1,'{$value->note}',NULL, NULL, {$value->total_price})";

				if(mysqli_query($con,$sql))
			    {
					$sqlE = "SELECT 1 from `event_extras` where  extra_descr='{$extra_descr}' and oid_user in (select oid_user from role_user_assoc where oid_user={$oid_user} and oid_role in (select oid from roles where acronym in ('MANAGER','EDITOR')))";
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
							$sqlInsEx = "INSERT INTO event_extras (oid, extra_descr, oid_user) VALUES ({$oidExtra}, '{$extra_descr}', {$oid_user})";
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

    if (!empty($_FILES["logo"]["name"])) {
			
			$userdir = $locationPackage.$oid;
		
			if(!file_exists($userdir)){
				mkdir($userdir, 0755, true );
			}
			$filename = $_FILES['logo']['name'];
			$userdir .= "/".$filename;
			$array = explode('.', $_FILES['logo']['name']);
			$extension = end($array);
		
			if ((($_FILES["logo"]["type"] == "image/gif")
			|| ($_FILES["logo"]["type"] == "image/jpeg")
			|| ($_FILES["logo"]["type"] == "image/png")
			|| ($_FILES["logo"]["type"] == "image/pjpeg")
			|| ($_FILES["logo"]["type"] == "image/jpg"))
			&& ($_FILES["logo"]["size"] < 10485760)
			&& in_array(strtolower($extension), $allowedExts)) {
				 move_uploaded_file($_FILES['logo']['tmp_name'],$userdir);
			}
			else {
				echo("Error description: File not allowed");
				http_response_code(500);
				exit;
			}
		
		}
		
		
		$con->close();
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