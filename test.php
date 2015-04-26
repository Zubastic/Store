<?php
    include_once 'DataBase.php';
    
    $db = DBWorkerFabric::GetDataBaseWorker();
    
    $re = $db->checkUser("Nagibator2003", "1111");
    print_r($re);
    //print_r($items);
?>