<?php
    include_once 'DataBase.php';
    include_once 'Tools/Item.php';
    include_once 'Autentication/Auth.php';
    include_once 'Tools/User.php';
    
    $auth = AuthFabric::GetAuth();
    $user = $auth->getCurrentUser();
?>

<!DOCTYPE html>
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
        <!--Верхнее меню-->
        <?php include 'Elements/headerMenu.php'; ?>

        <!--Поиск-->
         <div class="headerSearchPanel">
         <div class="container">
            <div class="Search">
                 <form>
                     <input class="SearchInput" type="text" name="name_contains" placeholder="Поиск">
                     <input class="ButtonSubmit" type="submit" value="Найти"> 
                 </form>
                </div>
             <div class="closer"></div>
          </div>
         </div>

         <!-- Боковое меню. -->
         <?php include 'Elements/leftMenu.php'; ?>

         <!-- Какие-то товары. -->
         <div class="containerItem">
               <div class="container">
                 <div class="ItemsMenu">
                     <ul>
                         
                         <?php
                            
                         
                            $searchQuery = new SearchQuery();
                            $searchQuery->CategoryIndex = htmlspecialchars($_GET['category']);
                            $searchQuery->Contains = htmlspecialchars($_GET['name_contains']);

                            $db = DBWorkerFabric::GetDataBaseWorker();
                            $items = $db->findItems($searchQuery);
                            
                            
                            if ($items != null)
                            {   
                                foreach ($items as $item) {
                                    echo '<li class="ItemList" >';
                                        if ($user != null && ($user->isAdmin() || $user->isModerator())) {
                                            $item->show(ItemElements::getAdminItemInfo());
                                        } else {
                                            $item->show(ItemElements::getMainMenu());
                                        }
                                    echo '</l>';
                                }
                            }
                            else
                            {
                                echo 'По Вашему запросу ничего не найдено.';
                            }
                         ?>
                     </ul>
                 </div>
                 <div class="closer"></div>
             </div>
         </div>
       
        <!--Футер-->
        <?php include 'Elements/footerMenu.php'; ?>

        <!--Окошко для зума изображения-->
        <?php include 'Elements/ImgZoomModal.php'; ?>

        <!--Окошко для логина-->
        <?php include 'Elements/UserLoginModalWnd.php'; ?>

        <!--Затемнитель-->
         <div class="fader"></div>
    </body>
</html>
