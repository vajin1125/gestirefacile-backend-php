<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';


//$postdata = file_get_contents("php://input");


//if(isset($postdata) && !empty($postdata))
if (!empty($_POST)) 
{
	/*$request = json_decode($postdata);
	
	
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
	
	
	$name = mysqli_real_escape_string($con, trim($_POST['name']));	
	$surname = mysqli_real_escape_string($con, trim($_POST['surname']));
	$email = mysqli_real_escape_string($con, trim($_POST['email']));
	$username = mysqli_real_escape_string($con, trim($_POST['username']));
	$pwd = $_POST['password'];
	$superuser = 0;
	$changepassword = 1;
	if ($_POST['enabled'] == 'true') {
		$enabled = 1;
	}
	else {
		$enabled = 0;
	}
	$maxbusinessnum = mysqli_real_escape_string($con, $_POST['max_business_num']);
	$tel = mysqli_real_escape_string($con, trim($_POST['tel']));
	$cell = mysqli_real_escape_string($con, trim($_POST['cell']));
	$image = mysqli_real_escape_string($con, trim($_POST['image']));
	
	$sql = "SELECT max(oid)+1 as id from users ";
	
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
	
	$oid_user = $currentUser['oid'];
	$sql = "INSERT INTO `users` (`oid`, `superuser`, `name`, `surname`, `username`, `password`, `email`, `enabled`, `tel`, `cell`, `max_business_num`, `change_password`,`image`,`oid_user_ref`) VALUES ('{$oid}','{$superuser}', '{$name}','{$surname}', '{$username}','{$pwd}','{$email}', '{$enabled}','{$tel}', '{$cell}', '{$maxbusinessnum}', '{$changepassword}', '{$image}', '{$oid_user}')";
	if(mysqli_query($con,$sql))
	{
		if ($_POST['role_user_assoc']) {
			$roleassoc = json_decode($_POST['role_user_assoc']);
			$_POST['role_user_assoc'] = $roleassoc;
			foreach ($roleassoc as $value)
			{
			   
			    $sql = "SELECT max(oid)+1 as id from `role_user_assoc` ";
	
			    if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidRoleAssoc = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidRoleAssoc == NULL) {
					$oidRoleAssoc = 0;
				}
				
			   
			   $sql = "INSERT INTO `role_user_assoc` (`oid`,`oid_user`,`oid_role`,`oid_business`) VALUES ('{$oidRoleAssoc}', '{$oid}', '{$value->role->oid}', '{$value->business->oid}')";
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
		
		
		
	
		
		$_POST['oid'] = $oid;
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