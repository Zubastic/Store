<?php
session_start();
include_once './DataBase.php';
include_once './Tools/User.php';

class AuthClass {
    
    // Интерфейс для работы с БД.
    private $_dbWorker = null;
    
    public function __construct($dbWorker) {
        $this->_dbWorker = $dbWorker;
    }
    
    /**
     * Проверяет, авторизован пользователь или нет
     * Возвращает true если авторизован, иначе false
     * @return boolean 
     */
    public function isAuth() {
        if (isset($_SESSION["is_auth"])) { //Если сессия существует
            return $_SESSION["is_auth"]; //Возвращаем значение переменной сессии is_auth (хранит true если авторизован, false если не авторизован)
        }
        else return false; //Пользователь не авторизован, т.к. переменная is_auth не создана
    }
    
    /**
     * Авторизация пользователя
     * @param string $login
     * @param string $passwors 
     */
    public function auth($login, $password) {
        // Существует ли такая пара логин/пароль.
        if ($this->_dbWorker->checkUser($login, $password)) { 
            $_SESSION["is_auth"] = true;
            $_SESSION["login"] = $login;
            return true;
        }
        else {
            $_SESSION["is_auth"] = false;
            return false;
        }
    }
    
    // Завершает сессию.
    public function out() {
        $_SESSION = array(); //Очищаем сессию
        session_destroy(); //Уничтожаем
    }
    
    //  Возвращет текущего пользователя.
    public function getCurrentUser() {
        if ($this->getCurrentLogin() != "")
        {
            return new User($this->getCurrentLogin(), $this->_dbWorker);
        }
        return null;
    }
    
    
    // Возвращает логин авторизованного пользователя
    private function getCurrentLogin() {
        if ($this->isAuth()) { //Если пользователь авторизован
            return $_SESSION["login"]; //Возвращаем логин, который записан в сессию
        } else {
            return "";
        }
    }
    
}

abstract class AuthFabric {
    private static $_auth = null;

    public static function GetAuth() {
        if (is_null(self::$_auth)) {
            self::$_auth = new AuthClass(DBWorkerFabric::GetDataBaseWorker());
        }
        return self::$_auth;
    }
}
