<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

//$postdata = file_get_contents("php://input");

//if(isset($postdata) && !empty($postdata))
if (!empty($_POST)) 
{
	//$request = json_decode($postdata);

	/*$oid = mysqli_real_escape_string($con, trim($request->oid));
	$name = mysqli_real_escape_string($con, trim($request->name));
	$surname = mysqli_real_escape_string($con, trim($request->surname));
	$email = mysqli_real_escape_string($con, trim($request->email));
	$username = mysqli_real_escape_string($con, trim($request->username));
	$pwd = $request->password;
	$superuser = 0;
	$changepassword = 1;
	$enabled = $request->enabled;
	$maxbusinessnum = mysqli_real_escape_string($con, (int)$request->max_business_num);
	$tel = mysqli_real_escape_string($con, trim($request->tel));
	$cell = mysqli_real_escape_string($con, trim($request->cell));
	$image = mysqli_real_escape_string($con, trim($request->image));*/
	
	
	$oid = $currentUser['oid'];
	$name = mysqli_real_escape_string($con, trim($_POST['name']));	
	$surname = mysqli_real_escape_string($con, trim($_POST['surname']));
	$email = mysqli_real_escape_string($con, trim($_POST['email']));
	$username = mysqli_real_escape_string($con, trim($_POST['username']));
	$pwd = $_POST['password'];
	$superuser = 0;
	$changepassword = 0;
	$tel = mysqli_real_escape_string($con, trim($_POST['tel']));
	$cell = mysqli_real_escape_string($con, trim($_POST['cell']));
	$image = mysqli_real_escape_string($con, trim($_POST['image']));
	
	
	
	
	$sql = "UPDATE `users` SET `name` = '{$name}', `surname` = '{$surname}', `username` = '{$username}', `password` = '{$pwd}', `email` = '{$email}', `tel` = '{$tel}', `cell` = '{$cell}',  `change_password` = '{$changepassword}', `image` = '{$image}'  WHERE `users`.`oid` = '{$oid}'";
	
	
	if(mysqli_query($con,$sql))
	{
		
		$sql = "DELETE FROM attributes where oid_user = {$oid}";
		
		if (mysqli_query($con,$sql) && $_POST['attribute_assoc']) {
			$attributeassoc = json_decode($_POST['attribute_assoc']);
			$_POST['attribute_assoc'] = $attributeassoc;
			foreach ($attributeassoc as $value)
			{
			   
				$sql = "SELECT max(oid)+1 as id from `attributes` ";
	
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
				$oidBusiness = 'NULL';
				if ($value->business) {
					$oidBusiness = $value->business->oid;
				}
			   
			    $sql = "INSERT INTO `attributes` (`oid`,`oid_attribute_type`,`oid_business`,`oid_user`,`name`) VALUES ({$oidAttributeAssoc}, {$value->type->oid}, {$oidBusiness}, {$oid}, '{$value->name}')";
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
		
		$con->close();
		
		if (!empty($_FILES["logo"]["name"])) {
			
			$userdir = $locationUser.$oid;
		
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
		
		//echo json_encode($request);
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