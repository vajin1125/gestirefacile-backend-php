<?php
	$oid_user = $currentUser['oid'];
	
	if ($oid_user != 1) {
		http_response_code(401);
		exit;
	}

?>