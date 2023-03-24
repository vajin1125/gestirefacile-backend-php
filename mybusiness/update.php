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
	
	
	$oid = mysqli_real_escape_string($con, trim($_POST['oid']));
	$name = mysqli_real_escape_string($con, trim($_POST['name']));	
	$descr = mysqli_real_escape_string($con, trim($_POST['descr']));	

	$logo = mysqli_real_escape_string($con, trim($_POST['logo']));	
	$address = mysqli_real_escape_string($con, trim($_POST['address']));
	$piva = mysqli_real_escape_string($con, trim($_POST['piva']));
	$tel = mysqli_real_escape_string($con, trim($_POST['tel']));
	$cell = mysqli_real_escape_string($con, trim($_POST['cell']));
	$email = mysqli_real_escape_string($con, trim($_POST['email']));
	$color = mysqli_real_escape_string($con, trim($_POST['color']));
		
	
	
	
	$sql = "UPDATE `business` SET `name` = '{$name}', `descr` = '{$descr}',  `logo` = '{$logo}', `address` = '{$address}', `piva` = '{$piva}', `tel` = '{$tel}', `cell` = '{$cell}', `email` = '{$email}', color = '{$color}'  WHERE `business`.`oid` = '{$oid}'";
	
	
	if(mysqli_query($con,$sql))
	{
		
		
		$sql = "DELETE FROM role_user_assoc where oid_business =  '{$oid}'";
		
		if (mysqli_query($con,$sql) && $_POST['role_user_assoc']) {
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
			   
			   $sql = "INSERT INTO `role_user_assoc` (`oid`,`oid_user`,`oid_role`,`oid_business`) VALUES ('{$oidRoleAssoc}', '{$value->user->oid}', '{$value->role->oid}', '{$oid}')";
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
		
		if (!empty($_FILES["file"]["name"])) {
			
			//$userdir = $location.$oid;
			$userdir = $locationBusiness.$oid;
			
			if(!file_exists($userdir)){
				mkdir($userdir, 0755, true );
			}
			$filename = $_FILES['file']['name'];
			$userdir .= "/".$filename;
			$array = explode('.', $_FILES['file']['name']);
			$extension = end($array);
		
			if ((($_FILES["file"]["type"] == "image/gif")
			|| ($_FILES["file"]["type"] == "image/jpeg")
			|| ($_FILES["file"]["type"] == "image/png")
			|| ($_FILES["file"]["type"] == "image/pjpeg")
			|| ($_FILES["file"]["type"] == "image/jpg"))
			&& ($_FILES["file"]["size"] < 10485760)
			&& in_array(strtolower($extension), $allowedExts)) {
				 move_uploaded_file($_FILES['file']['tmp_name'],$userdir);
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