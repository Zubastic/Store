<?php

include 'Tools/UserInfo.php';
include_once 'Tools/Item.php';
include_once 'Tools/Order.php';
//include_once 'Category.php';


// TODO: Вынести в отдельный файл все связанное с поиском.
class SearchQuery {
    public $CategoryIndex = 0;
    public $Id = -1;
    // TODO: Добавить еще.
    public $Contains = "";
}


// Интерфейс для БД. Я пытался разбить на несколько, но задолбался и забил.
interface IDataBaseWorker {
    // Для вывода в левом меню.
    function getCategories();                   // array(name, ref)
    
    function findUser($login);                  // bool
    function checkUser($login, $pwd);           // bool
    function addUser($login, $pwd);             // bool
    
    function setUserInfo($login, $userInfo);    // void
    function getUserInfo($login);               // UserInfo  
    function addItemToCart($login, $itemId);    // void
    function findItems($searchQuery);           // Item[]
    function getItem($id);                      // Item
    function getCart($login);                   // Item[]
    function clearCart($login);                 // void
    function getOrders($login);                 // Orders[]
    function addOrder($login, $order);          // void
    
    // Оформить заказ из корзины.
    function checkoutCart($login);              // void
    function getOrder($login, $num);            // Order
}


abstract class DBWorkerFabric {
    private static $_dbWorker = null;
    
    // TODO: Сделать отдельные классы 
    public static function GetDataBaseWorker() {
        if (is_null(self::$_dbWorker)) {
            self::$_dbWorker = new DataBaseWorker();
        }
        return self::$_dbWorker;
    }
}


class DataBaseWorker implements IDataBaseWorker {
    private $database = null;

    public function __construct() {
        include('db.conf.php');
        try {
            $this->database = new PDO("$dbtype:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword);
            $this->database->exec("SET NAMES 'utf8'");
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

    public function addItemToCart($login, $itemId) {
        
    }

    public function addOrder($login, $order) {
        
    }

    public function addUser($login, $pwd) {
        
    }

    public function checkUser($login, $pwd) {
        $slogin = htmlspecialchars($login);
        $spwd = htmlspecialchars($pwd);
        $info = $this->database->query("CALL CHECK_USER('$slogin', '$spwd')");
        return $info->rowCount() > 0;
    }

    public function checkoutCart($login) {
        
    }

    public function clearCart($login) {
        
    }
    
    // Возвращает массив Item-ов.
    public function findItems($searchQuery) {
        if (is_null($searchQuery)) {
            return array();
        }
        $ssearchQuery = new SearchQuery();
        $ssearchQuery->CategoryIndex = htmlspecialchars($searchQuery->CategoryIndex);
        $ssearchQuery->Contains = htmlspecialchars($searchQuery->Contains);
        
        $category = 0;
        if ($searchQuery->CategoryIndex > 0) {
            $category = $ssearchQuery->CategoryIndex;
        }
        
        $info = $this->database->query("CALL FIND_ITEMS($category,'$ssearchQuery->Contains')");
        $arr = $info->fetchAll();
        
        foreach($arr as $itemArr) {
            $items[] = new Item($itemArr);
        }
        
        return $items;
    }

    public function findUser($login) {
        $slogin = htmlspecialchars($login);
        $info = $this->database->query("CALL FIND_USER('$slogin')");
        return $info->rowCount() > 0;
    }

    public function getCart($login) {
        
    }

    // Возвращает массив имен категорий.
    public function getCategories() {
        $info = $this->database->query("CALL GET_CATEGORIES()");
        
        for ($i = 0; $i < $info->rowCount(); $i++) {
            $result[] = $info->fetchColumn();
        }
        return $result;
    }

    public function getItem($id) {
        
    }

    public function getOrder($login, $num) {
        
    }

    public function getOrders($login) {
        
    }

    public function getAllOrders($login) {
        
    }
    
    public function getUserInfo($login) {
        
    }

    public function setUserInfo($login, $userInfo) {
        
    }

    
    // TODO: Использовать стандартную функцию.
    private function itemsArrayIntrsect($arr1, $arr2) {
        $result = array();
        foreach ($arr1 as $item1) {
            foreach ($arr2 as $item2) {
                if ($item1->Id === $item2->Id) {
                    $result[] = $item1;
                }
            }
        }
            return $result;
    }
}


// Класс для фейкового набора данных.
abstract class Data {
    public static $goods = array(
        array(name => "Броня 1", id => "123", imgSrc => "img/armor_1.png", price => 1234, count => 5,
            description => "Броня норм", category => "Armor"),
        
        array(name => "Броня 2", id => "1234", imgSrc => "img/armor_2.png", count => 10,
        price => 431, description => "Броня д", category => "Armor"),
        
        array(name => "Лук", id => "125", imgSrc => "img/bow.png", count => 35,
        price => 52, description => "лук как лук", category => "Bow"),
        
        array(name => "Криворукий меч", id => "1", imgSrc => "img/t_blade.png", count => 2,
        price => 1234, description => "У воинов из Хаммерфелла кривые мечи... Кривые. Мечи.", category => "Weapon"),
        
        array(name => "Однорукий меч", id => "124", imgSrc => "img/o_blade.png", count => 10,
        price => 431, description => "лук как меч", category => "Weapon"),
        
        array(name => "Щит", id => "126", imgSrc => "img/shield.png", count => 53,
        price => 3433, description => "холи", category => "Shield")
    );
    
    public static $categories = array(array(name => "Луки и стрелы", ref => "index.php?items_category=Bow"),
                                   array(name => "Броня", ref => "index.php?items_category=Armor"),
                                   array(name => "Щиты", ref => "index.php?items_category=Shield"),
                                   array(name => "Оружие", ref => "index.php?items_category=Weapon"));
    
    
    
    public static $users = array(
        array(login => "Nagibator2004",
            name => "Ванечка", midname => "Иванович", surname => "Пупкин",
            address => "г.Мухосранск, ул.Ленина, д.3, кв.2",
            password => "1234", privileges => 1,
            contacts => array(array(type => 0, data => "ololo@dew.com"), array(type => 1, data => "31256543"),
                array(type => 1, data => "123489234"),array(type => 0, data => "nAgibAt0rR2004@dota.com"))),
        array(login => "STRIKER_2003", name => "Петр", midname => "Евгеньевич", surname => "Иванов",
            address => "NY, Манхэттн",
            password => "4321", privileges => 1,
            contacts => array(array(type => "Phone", data => "123456789"), array(type => 0, data => "STRIKER_2OO3@cstrike.ru")))
        );
    
    
    public static $orders = array(
            array(number => 1, user => "Nagibator2004", itemList => array(125,124,123), date => "21.03.2015"), 
            array(number => 4, user => "Nagibator2004", itemList => array(126,124,123), date => "22.03.2015"), 
            array(number => 2, user => "STRIKER_2003", itemList => array(1234,125,126), date => "23.04.2015")
        );
}




class FakeDataBaseWorker implements IDataBaseWorker {
    
    
    
    public function addUser($login, $pwd) {
        if ($this->findUser($login)) {
            return false;
        }
        Data::$users[] = array(login => $login, password => $pwd);
        return true;
    }

    public function checkUser($login, $pwd) {
        foreach (Data::$users as $user) {
            if ($user['login'] == $login && 
                $user['password'] == $pwd) 
            {
                return true;
            }
        }
        return false;
    }

    public function findUser($login) {
        foreach (Data::$users as $usr) {
            if ($usr['login'] == $login) {
                return true;
            }
        }
        return false;
    }

    public function getCategories() {
        return Data::$categories;
    }

    public function getUserInfo($login) {
        foreach (Data::$users as $usr) {
            if ($usr['login'] == $login) {
                return new UserInfo($usr);
            }
        }
        return null;
    }

    public function setUserInfo($login, $userInfo) {
        Data::$users[0]['login'] = $login;
        Data::$users[0]['userInfo'] = $userInfo;
    }
    
    public function getCart($login) {
        $q = new SearchQuery();
        $q->Contains = "и";
        $high = $this->findItems($q);
        return $high;
    }

    public function getOrder($login, $num) {
        foreach ($this->getOrders($login) as $order) {
            if ($order->Number == $num) {
                return $order;
            }
        }
        return null;
    }

    public function getOrders($login) {
        $orderList = array();
        foreach (Data::$orders as $order) {
            if ($order['user'] == $login) {
                $orderList[] = new Order($order);
            }
        }
        return $orderList;
    }

    public function addItemToCart($login, $itemId) {
        // TODO: Реализовать.        
    }

    public function getItem($id) {
        foreach (Data::$goods as $item) {
            if ($item['id'] == $id) {
                return new Item($item);
            }
        }
        return null;
    }

    public function findItems($searchQuery) {
        
        $itemsByCategory = array();
        
        if ($searchQuery->CategoryName != "") {
            foreach (Data::$goods as $item) {
                if ($item['category'] == $searchQuery->CategoryName) {
                    $itemsByCategory[] = new Item($item);
                }
            }
        } else {
            foreach (Data::$goods as $value) {
                $itemsByCategory[] = new Item($value);
            }
        }
        
        
        $itemsByContained = array();
        
        if ($searchQuery->Contains != "") {
            $queryName = mb_convert_case($searchQuery->Contains, MB_CASE_LOWER, "UTF-8");
        
            foreach (Data::$goods as $item) {
                if (strpos(mb_convert_case($item['name'], MB_CASE_LOWER, "UTF-8"), $queryName) !== false) {
                    $itemsByContained[] = new Item($item);
                }
            }
        } else {
            foreach (Data::$goods as $value) {
                $itemsByContained[] = new Item($value);
            }
        }
        
        return $this->itemsArrayIntrsect($itemsByCategory, $itemsByContained);
        
    }
    
    // TODO: Использовать стандартную функцию.
    private function itemsArrayIntrsect($arr1, $arr2) {
        $result = array();
        foreach ($arr1 as $item1) {
            foreach ($arr2 as $item2) {
                if ($item1->Id === $item2->Id) {
                    $result[] = $item1;
                }
            }
        }
            return $result;
    }

    public function clearCart($login) {
        
    }

    public function addOrder($login, $order) {

    }

    public function checkoutCart($login) {
        $this->addOrder($login, $this->getCart($login));
        $this->clearCart($login);
    }

}
?>
