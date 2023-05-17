<?php 

// database connection will be here
require '../config/security.php';

$oid_user = $currentUser['oid'];

if (isset($_GET['oid'])) 
{ // if entertrainer
    $sql = "SELECT * 
        FROM entertrainer_availabilities e 
        WHERE entertrainer_oid={$oid_user}";

    if ($result = mysqli_query($con, $sql)) 
    {   
		$newArr = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $newArr[] = $row;
        }
        $con->close();
        echo json_encode($newArr);
    }
} 
else 
{ // if manager
    // $sql = "SELECT * 
    //     FROM entertrainer_availabilities e 
    //     WHERE business_oid 
    //     IN (
    //         SELECT oid_business 
    //         FROM role_user_assoc 
    //         WHERE oid_user={$oid_user} 
    //             AND oid_role 
    //             IN (
    //                 SELECT oid 
    //                 FROM roles
    //                 WHERE acronym
    //                     IN ('MANAGER', 'EDITOR')
    //             )
    //     )";
    $sql = "SELECT * FROM entertrainer_availabilities e ";

    if ($result = mysqli_query($con, $sql)) 
    {   
		$newArr = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $newArr[] = $row;
        }
        $con->close();
        echo json_encode($newArr);
    }
}

?>