<?php
// database connection will be here
require '../config/core.php';
require '../config/database.php';
//require '../config/database.php';
require '../mail/send.php';

if(isset($_GET['email'])) {
	$email = $_GET['email'];
	$sql = "SELECT email,username FROM users where email='{$email}'";
	if($result = mysqli_query($con,$sql))
	{
		if ($row = mysqli_fetch_row($result))  {
			$email = $row[0];
			$username = $row[1];
			$date = new DateTime();
			$long = $date->getTimestamp();
			$str = $email.$username.$secretkey.$long;
			$reset_token = hash('sha256',$str);
			$sqlUpd = "update users set reset_token='{$reset_token}' where email='{$email}'";
			if(mysqli_query($con,$sqlUpd))
			{
				$mail = new Email();
				$to      = "{$email}";
				$subject = 'GestireFacile.it Reset Password';
				$message = 'Clicca sul link per reimpostare la password <a href=https://gestirefacile.it/portal/auth/reset-password?token='.$reset_token.'>Clicca qui!</a><br>';
				$message .= 'Se non sei tu ad aver richiesto il reset della password ti basta cancellare questa email.<br><br><br>';
				$message .= 'Lo staff di GestireFacile.it.<br>';
				$mail->send($to, $subject, $message);
			}
		}
	}
}
	
?>