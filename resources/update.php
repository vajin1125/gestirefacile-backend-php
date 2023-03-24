<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

if (!empty($_POST)) 
{
	$create_user = 0;
	$oid = mysqli_real_escape_string($con, trim($_POST['oid']));
	$resource_type = json_decode($_POST['resourceType']);
	$_POST['resourceType'] = $resource_type;
	if (!empty($_POST['user_resource'])) {
		$user_resource = json_decode($_POST['user_resource']);
		$_POST['user_resource'] = $user_resource;
	}
	$oid_resource_type = $resource_type->oid;
	$oid_user = $currentUser['oid'];
	$code = mysqli_real_escape_string($con, trim($_POST['code']));
	$name = mysqli_real_escape_string($con, trim($_POST['name']));
	$surname = mysqli_real_escape_string($con, trim($_POST['surname']));
	$descr = mysqli_real_escape_string($con, trim($_POST['descr']));
	if ($_POST['available'] == 'true') {
		$available = 1;
	}
	else {
		$available = 0;
	}
	if (isset($_POST['avail_qta'])) {
		$avail_qta = mysqli_real_escape_string($con, trim($_POST['avail_qta']));
	}
	else {
		$avail_qta = '1';
	}
	/*if ($_POST['internal_working_price']) {
		$internal_working_price = mysqli_real_escape_string($con, trim($_POST['internal_working_price']));
	}
	else {
		$internal_working_price = 'NULL';
	}*/
	
	
	$image = mysqli_real_escape_string($con, trim($_POST['image']));
	if (!empty($_POST['email'])) {
		$email = "'".mysqli_real_escape_string($con, trim($_POST['email']))."'";
		$sql = "SELECT 1 from resources where email={$email} and  oid_user={$oid_user} and oid!= {$oid}";
	
		if($result = mysqli_query($con,$sql)) {
			if ($row = mysqli_fetch_row($result))  {
				echo("Email già presente!");
				http_response_code(501);
				exit;
			}
		}
	}
	else {
		$email = 'NULL';
	}
	$tel = mysqli_real_escape_string($con, trim($_POST['tel']));
	$cell = mysqli_real_escape_string($con, trim($_POST['cell']));
	$gender = mysqli_real_escape_string($con, trim($_POST['gender']));
	if ($_POST['own_car'] == 'true') {
		$own_car = 1;
	}
	else {
		$own_car = 0;
	}
	$note = mysqli_real_escape_string($con, trim($_POST['note']));
	if ($_POST['create_user'] == 'true') {
		//INSERT PORTAL USER
		$create_user = 1;
	}
	else {
		$create_user = 0;
	}
	if ($_POST['width']) {
		$width = mysqli_real_escape_string($con, trim($_POST['width']));
	}
	else {
		$width = 'NULL';
	}
	if ($_POST['height']) {
		$height = mysqli_real_escape_string($con, trim($_POST['height']));
	}
	else {
		$height = 'NULL';
	}
	if ($_POST['deep']) {
		$deep = mysqli_real_escape_string($con, trim($_POST['deep']));
	}
	else {
		$deep = 'NULL';
	}
	if ($_POST['weight']) {
		$weight = mysqli_real_escape_string($con, trim($_POST['weight']));
	}
	else {
		$weight = 'NULL';
	}
	
	$position = mysqli_real_escape_string($con, trim($_POST['position']));
	$capacity = mysqli_real_escape_string($con, trim($_POST['capacity']));
	if ($_POST['consumable'] == 'true') {
		$consumable = 1;
	}
	else {
		$consumable = 0;
	}
	
	

		
	if ($_POST['code']) {
		$sql = "SELECT 1 from resources where code='{$code}' and  oid_user={$oid_user} and oid!= {$oid}";
	
		if($result = mysqli_query($con,$sql)) {
			if ($row = mysqli_fetch_row($result))  {
				echo("Codice già presente!");
				http_response_code(501);
				exit;
			}
		}
	}
	else {
		if ($_POST['categories_assoc']) {
			$categoryAssoc = json_decode($_POST['categories_assoc']);
			if (empty($categoryAssoc)) {
				echo("Se non specifichi il codice devi selezionare almeno una categoria!");
				http_response_code(501);
				exit;
			}
			else {
				$newcode = $categoryAssoc[0]->acronym;
				$newcode = strtoupper(substr($newcode, 0, 3)).'-';
				$sql = "SELECT ifnull(max(CAST(SUBSTRING_INDEX(code, '-', -1) AS SIGNED)),0)+1 as seq  from resources where code like '{$newcode}%' and  oid_user={$oid_user} and oid!= {$oid}";
				
				if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$seq = $row[0];
						$newcode = $newcode.$seq;
					}
				}
				
				$code = $newcode;
				$_POST['code'] = $code;
			}
			
		}
		else {
			echo("Se non specifichi il codice devi selezionare almeno una categoria!");
			http_response_code(501);
			exit;
		}
	}
	
	
	
	
	$sqlUpd = "UPDATE resources SET `code`= '{$code}',`name`= '{$name}', `surname`= '{$surname}', `descr`= '{$descr}', `available`= {$available}, `avail_qta`= {$avail_qta}, `image`= '{$image}', `email`= {$email},`tel`= '{$tel}',`cell`= '{$cell}',`gender`= '{$gender}',`own_car`= {$own_car},`note`= '{$note}',`width`= {$width},`height`= {$height},`deep`= {$deep},`weight`= {$weight},`position`= '{$position}',`capacity`= '{$capacity}',`consumable`= {$consumable} where oid={$oid}";
	
	if(mysqli_query($con,$sqlUpd))
	{
		$sql = "DELETE FROM resource_resources_assoc where oid_resource =  '{$oid}'";
		if (mysqli_query($con,$sql) && $_POST['resources_assoc']) {
			$resourceAssoc = json_decode($_POST['resources_assoc']);
			$_POST['resources_assoc'] = $resourceAssoc;
			foreach ($resourceAssoc as $value)
			{
			   
			   $sql = "SELECT max(oid)+1 as id from `resource_resources_assoc` ";
	
			    if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidResourceAssoc = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidResourceAssoc == NULL) {
					$oidResourceAssoc = 0;
				}
				
			   
			   $sql = "INSERT INTO `resource_resources_assoc` (`oid`,`oid_resource`,`oid_resource_assoc`,`qta`) VALUES ('{$oidResourceAssoc}', '{$oid}', '{$value->resourceAssoc->oid}', {$value->qta})";
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
		
		$sql = "DELETE FROM category_resource_assoc where oid_resource =  '{$oid}'";
		if (mysqli_query($con,$sql) && $_POST['categories_assoc']) {
			$categoryAssoc = json_decode($_POST['categories_assoc']);
			$_POST['categories_assoc'] = $categoryAssoc;
			foreach ($categoryAssoc as $value)
			{
			   
			   $sql = "SELECT max(oid)+1 as id from `category_resource_assoc` ";
	
			    if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidCategoryAssoc = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidCategoryAssoc == NULL) {
					$oidCategoryAssoc = 0;
				}
				
			   
			   $sql = "INSERT INTO `category_resource_assoc` (`oid`,`oid_resource`,`oid_category`) VALUES ('{$oidCategoryAssoc}', '{$oid}', '{$value->oid}')";
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
		$sql = "DELETE FROM resource_skill_assoc where oid_resource =  '{$oid}'";
		if (mysqli_query($con,$sql) && $_POST['skills_assoc']) {
			$skillAssoc = json_decode($_POST['skills_assoc']);
			$_POST['skills_assoc'] = $skillAssoc;
			foreach ($skillAssoc as $value)
			{
			   
			   $sql = "SELECT max(oid)+1 as id from `resource_skill_assoc` ";
	
			    if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidSkillAssoc = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidSkillAssoc == NULL) {
					$oidSkillAssoc = 0;
				}
				
			   
			   $sql = "INSERT INTO `resource_skill_assoc` (`oid`,`oid_resource`,`oid_skill`) VALUES ('{$oidSkillAssoc}', '{$oid}', '{$value->oid}')";
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
		
		
		$sql = "DELETE FROM prices where oid_resource =  '{$oid}'";
		if (mysqli_query($con,$sql) && $_POST['prices']) {
			$prices = json_decode($_POST['prices']);
			$_POST['prices'] = $prices;
			foreach ($prices as $value)
			{
			   
				$sql = "SELECT max(oid)+1 as id from `prices` ";
	
			    if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidPrice = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidPrice == NULL) {
					$oidPrice = 0;
				}
				
				if ($value->price) {
					$price = mysqli_real_escape_string($con, trim($value->price));
					if (substr_count($price, ".") >=1 && substr_count($price, ",") == 1) {
						$price = str_replace(".", "", $price);
						$price = str_replace(",", ".", $price);
					} else if (substr_count($price, ".") ==0 && substr_count($price, ",") == 1) {
						$price = str_replace(",", ".", $price);
					}
				}
				else {
					$price  = 'NULL';
				}
				
				if ($value->default == 'true') {
					$default = 1;
				}
				else {
					$default = 0;
				}
			    $descr_price = mysqli_real_escape_string($con, trim($value->descr));
			    $sql = "INSERT INTO `prices` (`oid`,`oid_resource`,`descr`,`price`,`default`) VALUES ('{$oidPrice}', '{$oid}', '{$descr_price}', {$price}, {$default})";
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
		
		if ($_POST['movements']) {
			$movements = json_decode($_POST['movements']);
			$_POST['movements'] = $movements;
			foreach ($movements as $value)
			{
				if ($oid_resource_type == 2 && $value->oid == ""){ //MATERIALE INSERIMENTO IN MAGAZZINO
					$sqlBus = "select oid_business from role_user_assoc where oid_user= {$oid_user}";
					if($resultBus = mysqli_query($con,$sqlBus)) {
						if ($rowBus = mysqli_fetch_row($resultBus))  {
							$oid_business = $rowBus[0];
							$sqlMag = "select oid from warehouse where oid_user={$oid_user} and oid_business={$oid_business}";
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
									
									$date= date("Y-m-d");
									$time=date("H:m");
									$datetime=$date."T".$time;
									$sqlInsMag = "INSERT INTO `warehouse_movements` (`oid`, `oid_event`, `oid_warehouse`, `oid_user_operation`, `oid_resource`, `oid_type_movement`, `qta`, `reason`, `date_ts`) VALUES ({$oidMov}, NULL, {$oidMag}, {$oid_user}, {$oid}, 1, {$value->qta}, '{$value->reason}', '{$datetime}')";
									if(mysqli_query($con,$sqlInsMag))
									{
										$value->oid = $oidMov;
										$value->date_ts = $datetime;
										$sqlUpdRes = "UPDATE resources set avail_qta = ifnull(avail_qta,0) + ({$value->qta}) where oid = {$oid}";
										if(mysqli_query($con,$sqlUpdRes)) 
										{
											//UPDATE RESOURCE OK
											if ($_POST['avail_qta']) {
												$_POST['avail_qta'] = $_POST['avail_qta'] + $value->qta;
											}
											else {
												$_POST['avail_qta'] = $value->qta;
											}
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
		
		if (!empty($_FILES["logo"]["name"])) {
			
			$userdir = $locationResource.$oid;
		
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
		
		
		if ($create_user == 1) {
			$sql = "SELECT max(oid)+1 as id from users ";
	
			if($result = mysqli_query($con,$sql)) {
				if ($row = mysqli_fetch_row($result))  {
					$oidNew = $row[0];
				}
				else {
					http_response_code(500);
				}
			}
			if ($oidNew == NULL) {
				$oidNew = 0;
			}
			
			
			$superuser = 0;
			$changepassword = 1;
			$enabled = 1;
			$maxbusinessnum = 0;
			$email = str_replace("'", "", $email);
			$username = $email;
			$pwd = hash('sha256', $email);
			
			$sql = "INSERT INTO `users` (`oid`, `superuser`, `name`, `surname`, `username`, `password`, `email`, `enabled`, `tel`, `cell`, `max_business_num`, `change_password`,`image`,`oid_user_ref`) VALUES ('{$oidNew}','{$superuser}', '{$name}','{$surname}', '{$username}','{$pwd}','{$email}', '{$enabled}','{$tel}', '{$cell}', '{$maxbusinessnum}', '{$changepassword}', '{$image}', '{$oid_user}')";
			
			
			if(mysqli_query($con,$sql)) {
				
				$sqlUpd = "update resources set oid_user_res={$oidNew} where oid={$oid}";
				if (mysqli_query($con,$sqlUpd)) {
					//UPDATE OK;
				}
				else {
					echo("Error description: " . mysqli_error($con));
					http_response_code(422);
				}
				
				
				
				$sql = "SELECT oid_business from `role_user_assoc` where oid_user = '{$oid_user}'";
				if($result = mysqli_query($con,$sql)) {
					while ($row = mysqli_fetch_row($result))  {
						$oidBusiness = $row[0];
						$sqlR = "SELECT max(oid)+1 as id from `role_user_assoc` ";
	
						if($resultR = mysqli_query($con,$sqlR)) {
							if ($rowR = mysqli_fetch_row($resultR))  {
								$oidRoleAssoc = $rowR[0];
							}
							else {
								http_response_code(500);
							}
						}
						if ($oidRoleAssoc == NULL) {
							$oidRoleAssoc = 0;
						}
						
					   
					   $sqlIns = "INSERT INTO `role_user_assoc` (`oid`,`oid_user`,`oid_role`,`oid_business`) VALUES ('{$oidRoleAssoc}', '{$oidNew}', (select oid from roles where acronym='RESOURCE'), '{$oidBusiness}')";
					   if(mysqli_query($con,$sqlIns))
					   {
						//OK
					   }
					   else {
							echo("Error description: " . mysqli_error($con));
							http_response_code(422);
					   }
					}
				}
				
				if (!empty($_FILES["logo"]["name"])) {
					
			
					$dir = $locationUser.$oidNew;
				
					if(!file_exists($dir)){
						mkdir($dir, 0755, true );
					}
					$filename = $_FILES['logo']['name'];
					$dir .= "/".$filename;
					
					
					$copied = copy($userdir , $dir);

					if ((!$copied)) 
					{
						echo("Error description: File not allowed");
						http_response_code(500);
						exit;
					}				
				}
				
				
				
				$sqlU = "SELECT u.* FROM users u where u.oid={$oidNew}";

				if($resultU = mysqli_query($con,$sqlU)) {
					if ($db_fieldU = mysqli_fetch_assoc($resultU))  {
						$_POST['user_resource'] = $db_fieldU;
					}
				}
				
			}
			else {
				echo("Error description: " . mysqli_error($con));
				http_response_code(422);
			}
		}
		
		$con->close();
		echo json_encode($_POST);

	}else
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