<?php

// database connection will be here
require '../config/security.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data)
{
    $oid = $data['id'];
    // var_dump($data);
    // $business_oid = null;
    $title = $data['title'];
    $start_date = $data['start'];
    $end_date = $data['end'];
    // $all_day = null;
    $location = $data['meta']['location'];
    $note = $data['meta']['notes'];
    $primary_color = $data['color']['primary'];
    $secondary_color = $data['color']['secondary'];

    $sql = "UPDATE entertrainer_availabilities SET 
            `title`='{$title}',
            `start_date`='{$start_date}',
            `end_date`='{$end_date}',
            `location`='{$location}',
            `note`='{$note}',
            `primary_color`='{$primary_color}',
            `secondary_color`='{$secondary_color}'
            WHERE oid={$oid}";

    if (mysqli_query($con, $sql))
    {
        echo json_encode($data);
    }
    else
    {
        echo("Error description: " . mysqli_error($con));
        http_response_code(422);
    }

    $con->close();
}
else
{
    //http_response_code(200);
	//header( "HTTP/1.1 200 OK" );
    http_response_code(500);
	exit;
}

?>