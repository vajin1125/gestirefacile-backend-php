<?php
// specify your own database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'Sql1513497_1');

// specify your own database credentials
// define('DB_HOST', '31.11.39.32');
// define('DB_USER', 'Sql1513497');
// define('DB_PASS', 'z8lb8r0070');
// define('DB_NAME', 'Sql1513497_1');

// get the database connection
function connect()
{
	$connect = mysqli_connect(DB_HOST ,DB_USER ,DB_PASS ,DB_NAME);
  // var_dump($connect);
	if (mysqli_connect_errno()) {
		die("Failed to connect:" . mysqli_connect_error());
	}

	mysqli_set_charset($connect, "utf8");

	return $connect;
}

$con = connect();

?>