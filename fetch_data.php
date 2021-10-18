<?php

include('database_connection.php');

$query = "SELECT * FROM tbl_sample ORDER BY id";
$statement = $connect->prepare($query);

if($statement->execute()){

    $result = $statement->setFetchMode(PDO::FETCH_ASSOC);
    foreach($statement->fetchAll() as $k=>$v) {
        $data[] =  $v;
      
    }
    echo json_encode($data);
}

?>