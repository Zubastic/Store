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
    function removeItem($id);                   // void
    function getCart($login);                   // Item[]
    function clearCart($login);                 // void
    function getAllOrders();                    // Orders[]
    function getOrders($login);                 // Orders[]
    function addOrder($order);          // void
    
    // Оформить заказ из корзины (Удаляет из корзины и помещает в заказы).
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

class DataBaseWorker {
    private $database = null;

    private function _getDB() {
        unset($this->database);
        
        include('db.conf.php');
        try {
            $this->database = new PDO("$dbtype:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword);
            $this->database->exec("SET NAMES 'utf8'");
            return $dbase;
        } catch (PDOException $ex) {
            echo $ex->getMessage();
        }
    }

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
        $this->_getDB();
        
        $slogin = htmlspecialchars($login);
        $sitemId = htmlspecialchars($itemId);
        
        $usrId = $this->findUser($slogin);
        $this->database->exec("INSERT INTO CustomerCart (Acc_Index, Item_Index, Quantity) VALUES ('$usrId', '$sitemId','1')");
        $this->_getDB();
    }

    public function addOrder($login, $order) {
        $this->_getDB();
        
        $slogin = htmlspecialchars($login);

        $userId = $this->findUser($slogin);
        if ($userId <= 0) {
            throw new InvalidArgumentException("Такого пользователя нет.");
        }
        try {
            // TODO: Научиться добавлять в связанные таблицы.
            // TODO: Добавить прочие поля.
            $ordNum = time();
            $values = "'$userId', '$ordNum'";
            $this->database->exec("INSERT INTO Orders (Acc_Index, Ord_Number) VALUES ($values)");

            // Индекс занесенного заказа.
            $info = $this->database->query("SELECT MAX(Ord_Index) FROM Orders");
            $ind = $info->fetchAll()[0]['MAX(Ord_Index)'];
            
            foreach($order->ItemList as $item) {
                $values = "'$ind', '$item->Id', '1'";
                $this->database->exec("INSERT INTO Orders_Info (Ord_Index, Goods_Index, Quantity) VALUES ($values)");
            }
        } catch (PDOException $ex) {
            throw $ex;
        } finally {
            $this->_getDB();
        }
    }

    public function addUser($login, $pwd) {
        $this->_getDB();
        
        $slogin = htmlspecialchars($login);
        $spwd = htmlspecialchars($pwd);
        
        // TODO: Хэшировать.
        $this->database->exec("INSERT INTO Accounts (Login, Password) VALUES ('$slogin','$spwd')");
        $this->_getDB();
    }

    public function checkUser($login, $pwd) {
        $this->_getDB();
        
        $slogin = htmlspecialchars($login);
        $spwd = htmlspecialchars($pwd);
        $info = $this->database->query("CALL CHECK_USER('$slogin', '$spwd')");
        $this->_getDB();
        return $info->rowCount() > 0;
    }

    public function checkoutCart($login) {
        $this->_getDB();
        
        $slogin = htmlspecialchars($login);
        
        $order = new Order();
        $order->ItemList = $this->getCart($slogin);
        $order->Date = DateTime::W3C;
        $order->Login = $slogin;
        
        $this->addOrder($slogin, $order);
        $this->clearCart($slogin);
        $this->_getDB();
    }

    public function clearCart($login) {
        $this->_getDB();
        
        $slogin = htmlspecialchars($login);
        $id = $this->findUser($slogin);
        
        $this->database->exec("CALL CLEAR_CART($id)");
        $this->_getDB();
    }
    
    // Возвращает массив Item-ов.
    public function findItems($searchQuery) {
        $this->_getDB();
        
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
        
        $this->_getDB();
        return $items;
    }

    public function findUser($login) {
        $this->_getDB();
        
        $slogin = htmlspecialchars($login);
        $info = $this->database->query("CALL FIND_USER('$slogin')");
        $this->_getDB();
        if ($info->rowCount() > 0) {
            return $info->fetchAll()[0]['Acc_Index'];
        } else {
            return -1;
        }
    }

    public function getCart($login) {
        $this->_getDB();
        
        // TODO: Учитывать количество.
        $slogin = htmlspecialchars($login);
        $info = $this->database->query("CALL GET_CART('$slogin')");
        
        $items = array();
        $arr = $info->fetchAll();
        foreach ($arr as $itemArr) {
            $item = new Item($itemArr);
            //$item->CountToOrder = $arr['Quantity'];
            $items[] = $item;
        }
        
        $this->_getDB();
        return $items;
    }

    // Возвращает массив имен категорий.
    public function getCategories() {
        $this->_getDB();
        
        $info = $this->database->query("CALL GET_CATEGORIES()");
        
        for ($i = 0; $i < $info->rowCount(); $i++) {
            $result[] = $info->fetchColumn();
        }
        $this->_getDB();
        return $result;
    }

    public function getItem($id) {
        $this->_getDB();
        
        $sid = htmlspecialchars($id);
        
        $info = $this->database->query("CALL GET_ITEM($sid)");
        $itemArr = $info->fetchAll()[0];
        
        $this->_getDB();
        return new Item($itemArr);
        
    }

    public function getOrder($num) {
        $this->_getDB();
        $snum = htmlspecialchars($num);
        
        
        $info = $this->database->query("CALL GET_ORDER($num)");
        //print_r($info);
        
        
        $order = new Order();
        $order->Number = $snum;
        $order->ItemList = array();
        
        $tmp = $info->fetchAll();
        foreach ($tmp as $item) {
            $order->ItemList[] = $this->getItem($item['Goods_Index']);
        }
        
        $this->_getDB();
        return $order;
    }


    
    public function getOrders($login) {
        $this->_getDB();
        
        $slogin = htmlspecialchars($login);
        $id = $this->findUser($slogin);
        $info = $this->database->query("CALL GET_ORDERS($id)");
        
        $orders = array();
        $tmp = $info->fetchAll();
        
        foreach ($tmp as $orderNum) {
            //print_r($orderNum);
            $orders[] = $this->getOrder($orderNum['Ord_Number']);
        }
        $this->_getDB();
        return $orders;
    }

    public function getAllOrders() {
        $this->_getDB();
        
        $info = $this->database->query("SELECT Ord_Number FROM Orders");
        $orders = array();
        
        foreach ($info->fetchAll()[0] as $orderNum) {
            $orders[] = $this->getOrder($orderNum);
        }
        $this->_getDB();
        return $orders;
    }
    
    public function getUserInfo($login) {
        $this->_getDB();
        
        $slogin = htmlspecialchars($login);
        $id = $this->findUser($slogin);
        
        $query = $this->database->query("CALL GET_USER_INFO($id)");
        $info = $query->fetchAll();
        
        $userInfo = new UserInfo($info[0]);
        
        $addr = array();
        foreach ($info as $value) {
            $a = new Address();
            $a->Country = $value['Country'];
            $a->City = $value['City'];
            $a->Addr = $value['Address'];
            $addr[] = $a;
        }
        
        $contacts = array();
        foreach ($info as $value) {
            $c = new Contact();
            $c->Type = $value['ContactType'];
            $c->Data = $value['Data'];
            $contacts[] = $c;
        }
        
        $userInfo->Address = $addr;
        $userInfo->Contacts = $contacts;
        
        $this->_getDB();
        return $userInfo;
    }

    public function setUserInfo($login, $userInfo) {
        
    }

    public function removeItem($id) {
        
    }
    
    public function deleteOrder($login, $num) {
        //TODO: проверить привелегии при исполнении (юзер или админ)
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

?>
