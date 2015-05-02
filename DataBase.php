<?php
error_reporting(0);
include 'Tools/User.php';
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
    function removeItem($login, $id);                   // void
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
    private $lastStatement = null;

    private function _makeQuery($login, $statement) {
        if(!is_null($this->lastStatement)) {
            $this->lastStatement->closeCursor();
        }
        $this->lastStatement = $this->database->query($statement);
        return $this->lastStatement;
    }

    private function _makeExec($login, $statement) {
        return $this->database->exec($statement);
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
        $slogin = htmlspecialchars($login);
        $sitemId = htmlspecialchars($itemId);
        
        $usrId = $this->findUser($slogin);
        $this->database->exec("INSERT INTO CustomerCart (Acc_Index, Item_Index, Quantity) VALUES ('$usrId', '$sitemId','1')");
    }
    
    public function removeItemFromCart($login, $itemId) {
        $s_login = htmlspecialchars($login);
        $s_itemId = htmlspecialchars($itemId);

        $usrId = $this->findUser($s_login);
        
        $this->_makeExec($s_login, "DELETE FROM CustomerCart WHERE Acc_Index=$usrId AND Item_Index=$s_itemId");
    }
    
    public function addOrder($login, $order) {
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
            $statement = $this->database->exec("INSERT INTO Orders (Acc_Index, Ord_Number) VALUES ($values)");

            // Индекс занесенного заказа.
            $info = $this->_makeQuery("","SELECT MAX(Ord_Index) FROM Orders");
            $ind = $info->fetchAll()[0]['MAX(Ord_Index)'];
            
            foreach($order->ItemList as $item) {
                $values = "'$ind', '$item->Id', '1'";
                $this->database->exec("INSERT INTO Orders_Info (Ord_Index, Goods_Index, Quantity) VALUES ($values)");
            }
        } catch (PDOException $ex) {
            throw $ex;
        }
    }

    public function addUser($login, $pwd) {
        $s_login = htmlspecialchars($login);
        $spwd = htmlspecialchars($pwd);
        
        // TODO: Хэшировать.
        $this->database->exec("INSERT INTO Accounts (Login, Password) VALUES ('$s_login','$spwd')");
        $uId = $this->findUser($s_login);
        $this->_makeExec($s_login, "INSERT INTO Customers_Address (Acc_Index) VALUES ('$uId')");
    }

    public function checkUser($login, $pwd) {
        $slogin = htmlspecialchars($login);
        $spwd = htmlspecialchars($pwd);
        $info = $this->_makeQuery("","CALL CHECK_USER('$slogin', '$spwd')");
        return $info->rowCount() > 0;
    }

    public function checkoutCart($login) {
        $slogin = htmlspecialchars($login);
        
        $order = new Order();
        $order->ItemList = $this->getCart($slogin);
        $order->Date = DateTime::W3C;
        $order->Login = $slogin;
        
        $this->addOrder($slogin, $order);
        $this->clearCart($slogin);
    }

    public function clearCart($login) {
        $slogin = htmlspecialchars($login);
        $id = $this->findUser($slogin);
      
        $statement = $this->database->exec("CALL CLEAR_CART($id)");
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
        
        $info = $this->_makeQuery("","CALL FIND_ITEMS($category,'$ssearchQuery->Contains')");
        $arr = $info->fetchAll();
        
        foreach($arr as $itemArr) {
            $items[] = new Item($itemArr);
        }
        return $items;
    }

    public function findUser($login) {
        $slogin = htmlspecialchars($login);
        $info = $this->_makeQuery("","CALL FIND_USER('$slogin')");

        if ($info->rowCount() > 0) {
            return $info->fetchAll()[0]['Acc_Index'];
        } else {
            return -1;
        }
    }

    public function getCart($login) {
        // TODO: Учитывать количество.
        $slogin = htmlspecialchars($login);
        $info = $this->_makeQuery("","CALL GET_CART('$slogin')");
        
        $items = array();
        $arr = $info->fetchAll();
        foreach ($arr as $itemArr) {
            $item = new Item($itemArr);
            //$item->CountToOrder = $arr['Quantity'];
            $items[] = $item;
        }
        return $items;
    }

    // Возвращает массив имен категорий.
    public function getCategories() {
        $info = $this->_makeQuery("","CALL GET_CATEGORIES()");
        
        for ($i = 0; $i < $info->rowCount(); $i++) {
            $result[] = $info->fetchColumn();
        }
        return $result;
    }

    public function getItem($id) {
        $sid = htmlspecialchars($id);
        
        $info = $this->_makeQuery("","CALL GET_ITEM($sid)");
        $itemArr = $info->fetchAll()[0];
        return new Item($itemArr);
        
    }

    public function getOrder($num) {
        $snum = htmlspecialchars($num);
        $info = $this->_makeQuery("","CALL GET_ORDER($num)");
        //print_r($info);
        
        $order = new Order();
        $order->Number = $snum;
        $order->ItemList = array();

        $tmp = $info->fetchAll();
        foreach ($tmp as $item) {
            $order->ItemList[] = $this->getItem($item['Goods_Index']);
        }
        return $order;
    }

    public function getOrders($login) {
        $slogin = htmlspecialchars($login);
        $id = $this->findUser($slogin);
        $info = $this->_makeQuery("","CALL GET_ORDERS($id)");
        
        $orders = array();
        $tmp = $info->fetchAll();
        
        foreach ($tmp as $orderNum) {
            //print_r($orderNum);
            $orders[] = $this->getOrder($orderNum['Ord_Number']);
        }
        return $orders;
    }

    public function getAllOrders() {
        $query = $this->_makeQuery("","SELECT Ord_Number FROM Orders");
        $info = $query->fetchAll();
        
        $orders = array();
        foreach ($info as $orderNum) {
            $orders[] = $this->getOrder($orderNum[0]);
        }
        return $orders;
    }
    
    public function getUserInfo($login) {
        $s_login = htmlspecialchars($login);
        $id = $this->findUser($s_login);
        
        $query = $this->_makeQuery($s_login,"CALL GET_USER_INFO($id)");
        $info = $query->fetchAll();
        $query->closeCursor();
        
        $userInfo = new UserInfo($info[0]);
        
        $query = $this->_makeQuery($s_login,"CALL GET_USER_ADDRESS($id)");
        $info = $query->fetchAll()[0];
        
        $a = new Address();
        $a->Country = $info['Country'];
        $a->City = $info['City'];
        $a->Addr = $info['Address'];
        $a->AdditionalInfo = $info['Additional_Info'];
        $addr = $a;

        $query = $this->_makeQuery($s_login, "CALL GET_USER_CONTACTS($id)");
        $info = $query->fetchAll();
        $contacts = array();
        foreach ($info as $value) {
            $c = new Contact();
            $c->Type = $value['ContactType'];
            $c->Data = $value['Data'];
            $contacts[] = $c;
        }
        
        $userInfo->Address = $addr;
        $userInfo->Contacts = $contacts;
        return $userInfo;
    }

    public function setUserInfo($login, $userInfo) {
        $s_login = htmlspecialchars($login);
        
        $s_name = $userInfo->Name;
        $s_midName = $userInfo->MidName;
        $s_surname = $userInfo->Surname;
        $s_priv = $userInfo->Privileges;
        if ($s_priv == "") {
            $s_priv = 1;
        }
        
        $s_addr = new Address();
        $s_addr->Country = htmlspecialchars($userInfo->Address->Country);
        $s_addr->City = htmlspecialchars($userInfo->Address->City);
        $s_addr->Addr = htmlspecialchars($userInfo->Address->Addr);
        $s_addr->AdditionalInfo = htmlspecialchars($userInfo->Address->AdditionalInfo);
        
        $s_contacts = array();
        foreach ($userInfo->Contacts as $value) {
            $s_contacts[] = new Contact(htmlspecialchars($value->Type), htmlspecialchars($value->Data));
        }
        
        $q = "UPDATE Accounts SET First_Name='$s_name', Last_Name = '$s_surname', Patronym = '$s_midName', Group_Index = $s_priv WHERE Login='$s_login'";
        $this->_makeExec($s_login, $q);
        
        $usrId = $this->findUser($s_login);
        
        // Адрес.
        $q = "UPDATE Customers_Address SET Country='$s_addr->Country', City='$s_addr->City', Address='$s_addr->Addr', Additional_Info='$s_addr->AdditionalInfo' WHERE Acc_Index=$usrId";
        $this->_makeExec($s_login, $q);

        foreach ($s_contacts as $contact) {
            $q = "UPDATE Customers_Contacts SET Data='$contact->Data' WHERE Acc_Index='$usrId' AND ContactType='$contact->Type'";
            $this->_makeExec($s_login, $q);
        }
    }

    public function removeItem($login, $id) {
        $s_login = htmlspecialchars($login);
        $s_id = htmlspecialchars($id);
        
        $this->_makeExec($s_login, "DELETE FROM Goods WHERE Goods_Index=$s_id");
    }
    
    public function removeOrder($login, $num) {
        $s_login = htmlspecialchars($login);
        $s_num = htmlspecialchars($num);
        $this->_makeExec($s_login, "DELETE FROM Orders WHERE Ord_Number=$s_num");
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
