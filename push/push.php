<?php


class Push{
	
	public function __construct(){
       
    }
	
	
	
	public function send($postRequest){
		/** Google URL with which notifications will be pushed */
		$url = "https://fcm.googleapis.com/fcm/send";
		/** 
		 * Firebase Console -> Select Projects From Top Naviagation 
		 *      -> Left Side bar -> Project Overview -> Project Settings
		 *      -> General -> Scroll Down and you will be able to see KEYS
		 */
		$subscription_key  = "key=AAAA77L-vMg:APA91bHrXs615UDr_r5HGyU4idfFgOowVXMtGIHOhUp7bNkksf3kskIm_53myIZuopVArz5SfNUGn9mRpToMAmQCNy2odhkA-yCZ-iNQnP8jr2GM_mgX0O_SApe2bxaRYQj5RkneKvuE";

		/** We will need to set the following header to make request work */
		$request_headers = array(
			"Authorization:" . $subscription_key,
			"Content-Type: application/json"
		);
		
		/** Data that will be shown when push notifications get triggered */
		/** CURL POST code */
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

		$season_data = curl_exec($ch);

		if (curl_errno($ch)) {
			print "Error: " . curl_error($ch);
			exit();
		}
		// Show me the result
		curl_close($ch);
		$json = json_decode($season_data, true);
		
		return $json;
	
	}

}
?>