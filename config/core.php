<?php
// show error reporting
error_reporting(E_ALL);
ini_set("display_errors", 1);
error_reporting(E_ALL);


// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, Latitude, Longitude, Token");
	header( "HTTP/1.1 200 OK" );
	exit;
}
  
// home page url
$home_url="http://localhost/api/";
  
// page given in URL parameter, default page is one
$page = isset($_GET['page']) ? $_GET['page'] : 1;
  
// set number of records per page
$records_per_page = 5;
  
// calculate for the query LIMIT clause
$from_record_num = ($records_per_page * $page) - $records_per_page;

$session_day = 10;

$secretkey = 'Pasquale79';


$locationUser = '../../images/users/';
$locationBusiness = '../../images/business/';
$locationResource = '../../images/resources/';
$locationPackage = '../../images/packages/';
$locationEvent = '../../docs/events/';
$allowedExts = array("jpg", "jpeg", "gif", "png");
$latitude = 0;
$longitude = 0;
$ip = '0.0.0.0';
$currentUser = null;


	/** 
	 * Get header Authorization
	 * */
	function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
		
        return $headers;
    }
	/**
	 * get access token from header
	 * */
	function getBearerToken() {
		$headers = getAuthorizationHeader();
		// HEADER: Get the access token from the header
		if (!empty($headers)) {
			if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
				return $matches[1];
			}
		}
		return null;
	}
	
	function getIPAddress() {  
		//whether ip is from the share internet  
		if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
			$ip = $_SERVER['HTTP_CLIENT_IP'];  
		}  
		//whether ip is from the proxy  
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
		}  
		//whether ip is from the remote address  
		else{  
			$ip = $_SERVER['REMOTE_ADDR'];  
		}  
		return $ip;  
	}
		


	function getJwt($fields = array(), $secretkey = NULL) {
	 
		$encoded_header = base64_encode('{"alg": "HS256","typ": "JWT"}');
	 
		$encoded_payload = base64_encode(json_encode($fields));
	 
		$header_payload = $encoded_header . '.' . $encoded_payload;
	 
		$signature = base64_encode(hash_hmac('sha256', $header_payload, $secretkey, true));
	 
		$jwt_token = $header_payload . '.' . $signature;
	 
		return $jwt_token;
	 
	}
	 
	function checkJwt($token = NULL, $secretkey = NULL) {
	 
		$jwt_values = explode('.', $token);
	 
		$recieved_signature = $jwt_values[2];
	 
		$recievedHeaderAndPayload = $jwt_values[0] . '.' . $jwt_values[1];
		$currentUser = json_decode($jwt_values[1]);
	 
		$resultedsignature = base64_encode(hash_hmac('sha256', $recievedHeaderAndPayload, $secretkey, true));
	 
		if ($resultedsignature == $recieved_signature) {
			
			return(true);
		}else{
			return(false);
		}
	 
	}
	
	function getPayloadJwt($token = NULL) {
		$tokenParts = explode(".", $token);  
		$tokenHeader = base64_decode($tokenParts[0]);
		$tokenPayload = base64_decode($tokenParts[1]);
		$jwtHeader = json_decode($tokenHeader);
		$jwtPayload = json_decode($tokenPayload, true);
		return $jwtPayload;
	}

	
	

?>