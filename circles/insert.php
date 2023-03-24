<?php
// database connection will be here
require '../config/security.php';


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
	
	
	$acronym = mysqli_real_escape_string($con, trim($_POST['acronym']));	
	$descr = mysqli_real_escape_string($con, trim($_POST['descr']));
	
	
	$sql = "SELECT max(oid)+1 as id from circles ";
	
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
	
	$sql = "INSERT INTO `circles` (`oid`,`oid_user`,`acronym`, `descr`) VALUES ('{$oid}','{$oid_user}','{$acronym}', '{$descr}')";
	if(mysqli_query($con,$sql))
	{
		if ($_POST['circle_customer_assoc']) {
			$customerassoc = json_decode($_POST['circle_customer_assoc']);
			$_POST['circle_customer_assoc'] = $customerassoc;
			foreach ($customerassoc as $value)
			{
			   
			   $sql = "SELECT max(oid)+1 as id from `circle_customer_assoc` ";
	
			    if($result = mysqli_query($con,$sql)) {
					if ($row = mysqli_fetch_row($result))  {
						$oidCustomerAssoc = $row[0];
					}
					else {
						http_response_code(500);
					}
			    }
				if ($oidCustomerAssoc == NULL) {
					$oidCustomerAssoc = 0;
				}
				
			   
			   $sql = "INSERT INTO `circle_customer_assoc` (`oid`,`oid_circle`,`oid_customer`) VALUES ('{$oidCustomerAssoc}', '{$oid}', '{$value->customer->oid}')";
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