<?php 
                //include_once 'Autentication/Auth.php';
                include_once 'DataBase.php';
               
                $login = htmlspecialchars($_POST['login']);
                $pwd = htmlspecialchars($_POST['pwd']);
                
                $db = DBWorkerFabric::GetDataBaseWorker();
                
                if ($login != "" && $pwd != "") {
                    if ($db->findUser($login) > 0) {
                        echo 'Пользователь с таким именем уже есть.';
                    } else {
                        $db->addUser($login, $pwd);
                        $handeled = true;
                        header('Refresh: 2; URL=index.php');
                        echo 'Регистрация завершена успешно';
                    }
                }
            ?>

<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <title>Регистрация</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div>
            <!--TODO: Оформить.-->
            <?php 
                if ($handeled) {
                    exit();
                }
            ?>
            <form action="registration.php" method="POST">
                <div>Логин: <input type="text" name="login" /></div>
                <div>Пароль: <input type="PASSWORD" name="pwd" /></div>

                <div><input type="submit" value="Зарегистрироваться"/> </div>
            </form>
        </div>
    </body>
</html>
