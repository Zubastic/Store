<?php

class User {
    private $_userInfo = null;
    private $_login = "";

    // Интерфейс для работы с БД.
    private $_dbWorker = null;
    

    public function __construct($login, $dbWorker) {
        $this->_dbWorker = $dbWorker;
        $this->_login = $login;
        if ($login == "") {
            $this->_userInfo = new UserInfo();            
        } else {
            $this->_userInfo = $this->_dbWorker->getUserInfo($login);
        }
    }

    public function getLogin() {
        return $this->_login;
    }
    
    public function getPrivileges() {
        return $this->_userInfo->Privileges;
    }
    
    public function getUserInfo() {
        return $this->_userInfo;
    }
    
    public function setUserInfo($userInfo) {
        $this->_userInfo = $userInfo;
        $this->_dbWorker->setUserInfo($this->_login, $userInfo);
    }
    
    /**
     * Проверяет, имеет ли пользователь доступ в админку
     * Возвращает true если имеет, иначе false
     * @return boolean 
     */
    public function isAdmin() {
        $priv = $this->_userInfo->Privileges;
        if ($priv == Privileges::$Root) {
            return true;
        }
        return false;
    }
    
    /**
     * Проверяет, имеет ли пользователь доступ в админку
     * Возвращает true если имеет, иначе false
     * @return boolean 
     */
    public function isModerator() {
        $priv = $this->_userInfo->Privileges;
        if ($priv == Privileges::$Moderator) {
            return true;
        }
        return false;
    }
}

class Address {
    public $Country = "";
    public $City = "";
    public $Addr = "";
    public $AdditionalInfo = "";
}

// TODO: Константы.
abstract class Privileges {
    public static $Guest = 0;
    public static $User = 1; // TODO: Константу сделать.
    public static $Moderator = 3; // TODO: Константу сделать.
    public static $Root = 7; // TODO: Константу сделать.
}

// TODO: Константы.
abstract class ContactType {
    public static $allTypes = array(Email => 0, Phone => 1);
    public static $Email = 0;
    public static $Phone = 1;
}

class Contact {
    public function __construct($type, $data) {
        $this->Type = $type;
        $this->Data = $data;
    }

    public $Type;       // ContactType
    public $Data = "";  // Str
}

// TODO: Вспомнить, чего еще добавить.
class UserInfo {
    public function __construct($array) {
        $this->Name = $array['First_Name'];
        $this->MidName = $array['Patronym'];
        $this->Surname = $array['Last_Name'];
        //$this->Address = new Address($array['address']);
        $this->Privileges = $array['privileges'];
        /*
        foreach ($array['contacts'] as $contact) {
            $this->Contacts[] = new Contact($contact['type'], $contact['data']);
        }
         */
    }

    public $Name = "";          // str
    public $MidName = "";       // str
    public $Surname = "";       // str
    public $Address = array();  // Address[]
    public $Contacts = array(); // Contact[]
    public $Privileges = 0;     // Privileges
}
?>