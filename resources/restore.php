<?php
require '../config/security.php';

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata)) {

  $request = json_decode($postdata);
  $resource_oid = $request->oid;

  $sql = "UPDATE `resources` SET `is_trash` = 0  WHERE oid={$resource_oid}";

  if(mysqli_query($con, $sql)) {
    // OK
  } else {
    echo("Error description: " . mysqli_error($con));
    http_response_code(500);
  }

  $con->close();
  echo json_encode($request);
} else {
  http_response_code(500);
	exit;
}
?>