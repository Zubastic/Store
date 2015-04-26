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
    
    
    // Если 0 - корзина.
    if ($orderNum == "" || $orderNum == 0) {
        $items = $dataBase->getCart($user->getLogin());
        $pageName = "Корзина";
    } else {
        $pageName = "Заказ №" . $orderNum;
        $order = $dataBase->getOrder($user->getLogin(), $orderNum);
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
                            
                            // Проверим добавление нового товара.
                            $id = htmlspecialchars($_POST)['id'];
                            if ($id != "") {
                                $dataBase->addItemToCart($user->getLogin(), $id);
                            }
                            
                            if (is_null($items)) {
                                echo 'Такого заказа нет';
                            } else {
                                foreach ($items as $item) {
                                    echo '<li class="ItemList">';
                                        $item->show(ItemElements::getFullItemInfo(true));
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
