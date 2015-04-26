<?php
    include_once 'DataBase.php';
    
    $db = DBWorkerFabric::GetDataBaseWorker();
    $order = new Order();
    $item = new Item();
    $item->Count = 4;
    $item->Id = 3;
    $order->ItemList[] = $item;
    try {
        $db->addOrder("Nagibator2003", $order);
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
    
    //print_r($items);
?>