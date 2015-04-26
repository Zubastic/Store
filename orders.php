<!DOCTYPE html>

<?php
    include_once "Autentication/Auth.php";
    include_once "Tools/User.php";
    include_once 'Tools/Order.php';
    include_once "DataBase.php";
    
    
    $auth = AuthFabric::GetAuth();
    if ($auth->isAuth()) {
        $user = $auth->getCurrentUser();
        $userInfo = $user->getUserInfo();
    } else {
        header('Refresh: 3; URL=../index.php');
        echo 'Пользователь не зарегистрирован.';
        exit;
    }
?>

<html>
    <head>
        <title>Заказы</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/Styles.css"> 
        <link rel="stylesheet" type="text/css" href="css/basket_styles.css">
        <script type="text/javascript" src="/script/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="/script/script.js"></script>
    </head>
    <body class="body">
        <?php include 'Elements/headerMenu.php'; ?>
        
        <div class="headerSearchPanel">
         <div class="container">
            <div class="Search">
                 <form>
                     <input class="SearchInput" type="text" hidden="true" placeholder="Поиск">
                     <input class="ButtonSubmit" type="submit" hidden="true" value="Найти"> 
                 </form>
                </div>
             <div class="closer"></div>
          </div>
         </div>
        
        
        <ul>
            <?php
                $db = DBWorkerFabric::GetDataBaseWorker();
                
                // Оформляем заказ
                if (htmlspecialchars($_POST['act']) == "checkout") {
                    $db->checkoutCart($user->getLogin());
                }
                
                $orders = $db->getOrders($user->getLogin());
                foreach ($orders as $order) {
                    printf('<li><a href="orderInfo.php?orderNum=%s">Заказ №%s,  Дата: %s</a></li>', 
                            $order->Number, $order->Number, $order->Date);
                }
            ?>
        </ul>
        
    </body>
</html>
