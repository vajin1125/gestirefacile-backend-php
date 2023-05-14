<?php
require '../config/security.php';

$oid_user = $currentUser['oid'];
var_dump($oid_user);

$backup_file = "/backups/event.sql";
$sql = "SELECT * INTO OUTFILE '$backup_file' FROM events WHERE oid_user_create='$oid_user'";
// $result = mysqli_query($con, $sql);

if(!$result = mysqli_query($con, $sql)) {
  die('Could not take data backup: ' . mysql_error());
}

// header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header("Cache-Control: no-cache, must-revalidate");
// header("Expires: 0");
header('Content-Disposition: attachment; filename="'.basename($backup_file).'"');
header('Content-Length: ' . filesize($filename));
// header('Pragma: public');

// flush();
readfile($backup_file);

$con->close();

?>