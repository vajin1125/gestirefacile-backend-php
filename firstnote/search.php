<?php
// database connection will be here
require '../config/security.php';
//require '../config/database.php';

$oid_user = $currentUser['oid'];

// $data = array([
//     'oid' => 2,
//   'income' => "1000",
//   'expenses' => '4515',
//   'category' => 'aaaaa',
//   'datetime' => '2022-03-27T12:00:00.000Z',
//   'description' => 'asdfasdfasdfadsfasdfadfadsfadsf'
//   ],
//   [
//     'oid' => 1,
//   'income' => "2000",
//   'expenses' => '3000',
//   'category' => 'bbbb',
//   'datetime' => '2022-03-28T12:00:00.000Z',
//   'description' => 'aaaaaa'
//   ]
// );

// echo json_encode($data);

$sql = "
  SELECT 
    payments.*, 
    events.from as eventFrom, 
    events.to as eventTo, 
    events.address as eventAddress, 
    payment_types.acronym as paymentTypeAcronym, 
    payment_methods.acronym as paymentMethodAcronym  
  FROM payments
  LEFT JOIN events ON events.oid=payments.oid_event
  LEFT JOIN payment_types ON payment_types.oid=payments.oid_payment_type
  LEFT JOIN payment_methods ON payment_methods.oid=payments.oid_payment_method
  ORDER BY payments.oid DESC
";

if($result = mysqli_query($con,$sql)) {
  $newArr = array();
  while ($db_field = mysqli_fetch_assoc($result)) {
    $newArr[] = $db_field;
  }
  $con->close();
  echo json_encode($newArr);
}

// echo json_encode('{ "message": "success" }');

// if(isset($_GET['oid'])) {
// 	$oid = $_GET['oid'];
// 	$sql = "SELECT * FROM todolist where oid='{$oid}' and oid_user={$oid_user}";

// 	if($result = mysqli_query($con,$sql))
// 	{
// 		$newArr = array();
// 		while ($db_field = mysqli_fetch_assoc($result)) {
// 			$newArr[] = $db_field;
// 		}
// 		$con->close();
// 		echo json_encode($newArr);

// 	}
// }
// else {
// 	$sql = "SELECT * FROM todolist where oid_user={$oid_user}  order by oid desc";

// 	if($result = mysqli_query($con,$sql))
// 	{
// 		$newArr = array();
// 		while ($db_field = mysqli_fetch_assoc($result)) {
// 			$newArr[] = $db_field;
// 		}
// 		$con->close();
// 		echo json_encode($newArr);

// 	}
// }


?>