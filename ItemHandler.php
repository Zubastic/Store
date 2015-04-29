<?php
    include_once 'DataBase.php';
    include_once 'Tools/Item.php';
    include_once 'Autentication/Auth.php';
    include_once 'Tools/User.php';
    
    $auth = AuthFabric::GetAuth();
    $user = $auth->getCurrentUser();
    
    if ($user == null) {return;}

    $id = htmlspecialchars($_POST['id']);
    if ($id == "") {return;}
    if (htmlspecialchars($_POST['removeItem'] == "true") ) {
        $db = DBWorkerFabric::GetDataBaseWorker();
        $db->removeItem($user->getLogin(), $id);
        //header('Refresh: 3; URL=./index.php');
        //echo 'Удалено.';
        //exit();
    }