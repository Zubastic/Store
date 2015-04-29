<!--TODO: Проверять пользователя на зареганность.-->
<?php
    include "Autentication/Auth.php";
    include_once "Tools/User.php";
    
    // Хз почему, но иначе в auth.php ничего не подключится само. TODO: убрать.
    include_once 'DataBase.php';
    
    $auth = AuthFabric::GetAuth();
    if ($auth->isAuth()) {
        $user = $auth->getCurrentUser();
    } else {
        header('Refresh: 3; URL=../index.php');
        echo 'Пользователь не зарегистрирован.';
        exit;
    }
    
    // Обрабатываем данные.
    if (isset($_POST['name'])) {
        $newUserInfo->Name = htmlspecialchars($_POST['name']);
        $newUserInfo->MidName = htmlspecialchars($_POST['midname']);
        $newUserInfo->Surname = htmlspecialchars($_POST['surname']);
        $newUserInfo->Address = new Address();
        $newUserInfo->Address->Country = htmlspecialchars($_POST['country']);
        $newUserInfo->Address->City = htmlspecialchars($_POST['city']);
        $newUserInfo->Address->Addr = htmlspecialchars($_POST['address']);
        $newUserInfo->Address->AdditionalInfo = htmlspecialchars($_POST['extraAddr']);

        for($i = 0; $i < count($_POST['contact']); $i++) {
            $type = htmlspecialchars($_POST['contact_type'][$i]);
            $data = htmlspecialchars($_POST['contact'][$i]);
            $newUserInfo->Contacts[] = new Contact($type, $data);
        }
        $user->setUserInfo($newUserInfo);
        
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <?php
            printf('<title>%s - инфо</title>', $user->getLogin());
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
                if (!is_null($newUserInfo)) {
                    echo 'Данные сохранены';
                }
                $userInfo = $user->getUserInfo();
                // TODO: Придумать другое имя для класса.
                printf('<div class="userInfo">');
                    printf('<div>Имя: <input type="text" name="name" value="%s"></input></div>',$userInfo->Name);
                    printf('<div>Отчество: <input type="text" name ="midname" value="%s"></input></div>',$userInfo->MidName);
                    printf('<div>Фамилия: <input type="text" name ="surname" value="%s"></input></div>',$userInfo->Surname);
                    
                    printf('<div>Адрес:</div>');
                    printf('<div>Страна: <input type="text" name ="country" value="%s"></input></div>',$userInfo->Address->Country);
                    printf('<div>Город: <input type="text" name ="city" value="%s"></input></div>',$userInfo->Address->City);
                    printf('<div>Адрес: <input type="text" name ="address" value="%s"></input></div>',$userInfo->Address->Addr);
                    printf('<div>Дополнительно: <input type="text" name ="extraAddr" value="%s"></input></div>',$userInfo->Address->AdditionalInfo);

                    printf('<div> Контакты: </div>');
                    for ($i = 0; $i < count($userInfo->Contacts); $i++) {
                        $contact = $userInfo->Contacts[$i];
                        
                        printf('<div>');
                        printf('<select class="cont%s" name="contact_type[]">', $i);
                        
                        // TODO: Выводить опции из массива.
                        // TODO: Жесть какая-то, потом исправить.
                        if ($contact->Type == ContactType::$Email) {
                            printf('<option selected value="Email">Email</option>');
                        } else {
                            printf('<option value="Email">Email</option>');                            
                        }
                        if ($contact->Type == ContactType::$Phone) {
                            printf('<option selected value="Phone">Phone</option>');
                        } else {
                            printf('<option value="Phone">Phone</option>');                        
                        }
                        
                        printf('</select>');
                        printf('<input class="cont%s" type="text" name ="contact[]" value="%s"></input>', $i, $contact->Data);
                        printf('<span class="cont%s"> <a onclick="removeContact(%s)">Удали</a></span>', $i, $i);
                        printf('</div>');
                        
                    }
                printf('</div>');
            ?>
            <a onclick="addContact()">Добавить контакт</a>
            <div><input type="submit" value="Сохранить"></input></div>
        </form>
    </body>
</html>
