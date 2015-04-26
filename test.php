<?php
    include_once 'DataBase.php';
    
    $db = DBWorkerFabric::GetDataBaseWorker();
    $order = new Order();
    $item = new Item();
    $item->Id = 1;
    $order->ItemList[] = $item;
    $item = new Item();
    $item->Id = 2;
    $order->ItemList[] = $item;
    try {
        $db->addOrder("Nagibator2003", $order);
    } catch (Exception $ex) {
        echo $ex->getMessage();
    }
    
    //print_r($items);
?>