<!DOCTYPE html>
<?php
    include_once "DataBase.php";
    include_once "Autentication/Auth.php";
?>

<html>
    <head>
        <title>Интернет-магазин</title>
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
        <?php include 'Elements/headerMenu.php'; ?>
         
        <!--Скрытый поиск-->
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
        
         <!-- Боковое меню. -->
         <?php include 'Elements/leftMenu.php'; ?>
            
         <!-- Товар. -->
         <div class="containerItem">
               <div class="container">
                 <div class="ItemsMenu">
                     <ul>
                         <li class="ItemList" > 
                            <?php 
                                $item = null;
                                $db = DBWorkerFabric::GetDataBaseWorker();
                                $item = $db->getItem(htmlspecialchars($_GET['item_id']));
                                if (is_null($item)) {
                                    echo '<div class="ErrorText">Такого продукта нет</div>';
                                } else {
                                    $item->show(ItemElements::getFullItemInfo($auth->isAuth()));
                                    //$item->show(true, );
                                }
                            ?>
                         </li>
                     </ul>
                 </div>
                 <div class="closer"></div>
             </div>
         </div>
    </div>
       
    <?php include 'Elements/footerMenu.php'; ?>

    <?php include 'Elements/ImgZoomModal.php'; ?>

    <?php include 'Elements/UserLoginModalWnd.php'; ?>

    <div class="fader"></div>
        
        
    </body>
     
    
    
    
</html>
