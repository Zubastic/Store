<!DOCTYPE html>

<!--TODO: Проверять пользователя на зареганность.-->

<?php
    include "Autentication/Auth.php";
    include_once "Tools/User.php";
    
    // Хз почему, но иначе в auth.php ничего не подключится само. TODO: убрать.
    include_once 'DataBase.php';
    
    $auth = AuthFabric::GetAuth();
    if ($auth->isAuth()) {
        $user = $auth->getCurrentUser();
        $userInfo = $user->getUserInfo();
    } else {
        header('Refresh: 3; URL=../index.php');
        echo 'Пользователь не зарегистрирован.';
        exit;
    }
    
    // Обрабатываем данные.
    $POST_MASS = htmlspecialchars($_POST);
    if (!is_null($POST_MASS)) {
        $newUserInfo->Name = $POST_MASS['name'];
        $newUserInfo->MidName = $POST_MASS['midname'];
        $newUserInfo->Surname = $POST_MASS['surname'];
        $newUserInfo->Address = $POST_MASS['address'];
        for($i = 0; $i < count($POST_MASS['contact']); $i++) {
            $newUserInfo->Contacts[] = new Contact($POST_MASS['contact_type'][$i],
                    $POST_MASS['contact'][$i]);
        }
        $user->setUserInfo($newUserInfo);
    }
?>

<html>
    <head>
        <?php
            printf('<title>%s - инфо</title>', $userInfo->Login);
        ?>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="css/Styles.css"> 
        <link rel="stylesheet" type="text/css" href="css/basket_styles.css">
        <script type="text/javascript" src="script/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="script/script.js"></script>
    </head>
    <body class="body">
        <?php include 'Elements/headerMenu.php'; ?>
        
        <form action="userInfo.php" method="POST">
            <?php
                // TODO: Придумать другое имя для класса.
                printf('<div class="userInfo">');
                    printf('<div>Имя: <input type="text" name="name" value="%s"></input></div>',$userInfo->Name);
                    printf('<div>Отчество: <input type="text" name ="midname" value="%s"></input></div>',$userInfo->MidName);
                    printf('<div>Фамилия: <input type="text" name ="surname" value="%s"></input></div>',$userInfo->Surname);
                    printf('<div>Адрес: <input type="text" name ="address" value="%s"></input></div>',$userInfo->Address);
                    
                    printf('<div> Контакты: </div>');
                    for ($i = 0; $i < count($userInfo->Contacts); $i++) {
                        $contact = $userInfo->Contacts[$i];
                        printf('<div>');
                        printf('<select name="contact_type[]">');
                        
                        // TODO: Выводить опции из массива.
                        // TODO: Жесть какая-то, потом исправить.
                        if ($contact->Type == ContactType::$Email) {
                            printf('<option selected value="0">Email</option>');
                        } else {
                            printf('<option value="0">Email</option>');                            
                        }
                        if ($contact->Type == ContactType::$Phone) {
                            printf('<option selected value="1">Phone</option>');
                        } else {
                            printf('<option value="1">Phone</option>');                        
                        }
                        
                        printf('</select>');
                        printf('<input type="text" name ="contact[]" id="%i" value="%s"></input>', $i, $contact->Data);
                        printf('</div>');
                    }
                printf('</div>');
            ?>
            <a onclick="AddContact()">Добавить контакт</a>
            <div><input type="submit" value="Сохранить"></input></div>
        </form>
    </body>
</html>
