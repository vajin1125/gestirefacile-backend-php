<?php

class Recaptcha{
	
	public function __construct(){
       
    }

	public function verifyResponse($recaptcha){
		
		$remoteIp = $this->getIPAddress();

		// Discard empty solution submissions
		if (empty($recaptcha)) {
			return array(
				'success' => false,
				'error-codes' => 'missing-input',
			);
		}

		$getResponse = $this->getHTTP(
			array(
				'secret' => '6Le1st8ZAAAAAE86Teb6neQOblZM5_RYSnI_04rz',
				'remoteip' => $remoteIp,
				'response' => $recaptcha,
			)
		);

		// get reCAPTCHA server response
		$responses = json_decode($getResponse, true);

		if (isset($responses['success']) and $responses['success'] == true) {
			$status = true;
		} else {
			$status = false;
			$error = (isset($responses['error-codes'])) ? $responses['error-codes']
				: 'invalid-input-response';
		}

		return array(
			'success' => $status,
			'error-codes' => (isset($error)) ? $error : null,
		);
	}


	private function getIPAddress(){
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
		{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		} 
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
		{
		 $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} 
		else 
		{
		  $ip = $_SERVER['REMOTE_ADDR'];
		}
		
		return $ip;
	}

	private function getHTTP($data){
		/*$url = 'https://www.google.com/recaptcha/api/siteverify?'.http_build_query($data);
		
		
		$response = file_get_contents("'".$url."'");*/
		
		$verify = curl_init();
		curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($verify, CURLOPT_POST, true);
		curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($verify);
		
		curl_close($verify);

		
		return $response;
	}
}