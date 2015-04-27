<!DOCTYPE html>
<?php
    include_once "Autentication/Auth.php";
    include_once "Tools/User.php";
    include_once "DataBase.php";
    include_once 'Tools/Item.php';
    
    $auth = AuthFabric::GetAuth();
    if ($auth->isAuth()) {
        $user = $auth->getCurrentUser();
        $userInfo = $user->getUserInfo();
    } else {
        header('Refresh: 3; URL=../index.php');
        echo 'Пользователь не зарегистрирован.';
        exit;
    }
    
    $dataBase = DBWorkerFabric::GetDataBaseWorker();
    // Номер заказа для отображения.
    $orderNum = htmlspecialchars($_GET['orderNum']);
    $orderDelete = htmlspecialchars($_GET['$orderDelete']);
    if ($orderDelete != "" && $user->isAdmin()) {
        header('Refresh: 3; URL=../admin.php');
        $order = $dataBase->removeOrder($user->getLogin(), $orderDelete);
        echo 'Заказ ' + $orderDelete + " удален!";
        exit;
    }
    
    // Если 0 - корзина.
    if ($orderNum == "" || $orderNum == 0) {
        $pageName = "Корзина";
        // Проверим добавление нового товара.
        $id = htmlspecialchars($_POST['id']);
        if ($id != "") {
            if (htmlspecialchars($_POST['remove'] == "true") ) {
                $dataBase->removeItemFromCart($user->getLogin(), $id);
            } else {
                $dataBase->addItemToCart($user->getLogin(), $id);
            }
        }
        $items = $dataBase->getCart($user->getLogin());
    } else {
        $pageName = "Заказ №" . $orderNum;
        $order = $dataBase->getOrder($orderNum);
        if (!is_null($order)) {
            $items = $order->ItemList;
        }
    }
    
    
?>

<html>
    <head>
        <title><?php printf($pageName) ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/Styles.css"> 
        <link rel="stylesheet" type="text/css" href="css/Styles_Items.css"> 
        
        <script src="script/jquery-1.10.2.min.js"></script>
        <script src="script/script.js"></script>
    </head>
    <body class="body">
       <!-- Ўапка -->
    <div class="header">
            
            <!--¬ерхн€€ панель ссылок:-->
        <?php include 'Elements/headerMenu.php'; ?>
         <!-- онец ¬ерхней панели ссылок-->
         
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
            
         <!--Кнопка для оформления заказа-->
        <?php
            if ($orderNum == 0 && count($items) > 0) {
                echo <<< EOL
                <div>
                    <form action="orders.php" method="POST">
                        <input type="text" hidden="true" name="act" value="checkout" />
                        <input type="submit" value="Оформить" />
                    </form>
                </div>
EOL;
            }
        ?>
         
         <!-- Какие-то товары. -->
         <div class="containerItem">
               <div class="container">
                 <div class="ItemsMenu">
                     <ul>
                         <?php
                            if (is_null($items)) {
                                echo 'Такого заказа нет';
                            } else {
                                foreach ($items as $item) {
                                    echo '<li class="ItemList">';
                                        if ($orderNum == "" || $orderNum == 0) {
                                            $itemStyle = ItemElements::getCartItemInfo();
                                        } else {
                                            $itemStyle = ItemElements::getFullItemInfo(true);
                                        }
                                        $item->show($itemStyle);
                                    echo '</li>';
                                }
                            }
                         ?>
                     </ul>
                 </div>
                 <div class="closer"></div>
             </div>
         </div>
    </div>
       
        
       
       <?php include 'Elements/footerMenu.php'; ?>
       
       <?php include 'Elements/ImgZoomModal.php'; ?>
    </body>
</html>
