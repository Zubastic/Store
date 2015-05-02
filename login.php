<?php
    include_once "Autentication/Auth.php";
    
    $action = htmlspecialchars($_GET['action']);
    
    $auth = AuthFabric::GetAuth();
    
    switch ($action) {
        case "logout":
            $auth->out();
            header('Refresh: 0; URL=index.php');
            break;
        
        case "login":
            $login = htmlspecialchars($_POST['login']);
            $pwd = htmlspecialchars($_POST['pwd']);
            if ($auth->auth($login, $pwd)) {
                header('Refresh: 0; URL=index.php');
            } else {
                header('Refresh: 3; URL=index.php');
                echo 'Ошибка авторизации.';
                exit;
            }
            break;
        
        default:
            header('Refresh: 3; URL=index.php');
            echo 'action: ' . $action;
            exit;
    }
?>