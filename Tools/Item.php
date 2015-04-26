<?php

    // TODO: Константы.
    abstract class ItemElements {
        public static $Name = 0x1;
        public static $Description = 0x2;
        public static $Img = 0x4;
        public static $CartBtn = 0x8;
        public static $InfoBtn = 0x10;
        public static $Price = 0x20;
        public static $Count = 0x40;
        
        public static function getMainMenu() {
            return ItemElements::$Name | ItemElements::$Img | ItemElements::$Description
                    | ItemElements::$Price | ItemElements::$InfoBtn;
        }
        
        public static function getFullItemInfo($isAuth) {
            $result = ItemElements::$Name | ItemElements::$Img | ItemElements::$Description
                    | ItemElements::$Price | self::$Count;
            if ($isAuth) {
                 $result |= self::$CartBtn;
            }
            return $result;
        }
        
    }
            
    class Item {
        public $Name = "";
        public $Id = "";
        public $ImgSrc = "";
        public $Price = 0;
        public $Count = 0;
        public $Description = "";
        public $Category = "";


        public function __construct($array) {
            $this->Name = $array['Name'];
            $this->Id = $array['Goods_Index'];
            $this->ImgSrc = $array['Img_Source'];
            $this->Price = $array['Price'];
            $this->Count = $array['Items_in_storage'];
            $this->Description = $array['Description'];
            $this->Category = $array['Type_Name'];
        }
        
        public function show($elementsToShow) {
            
            if ($elementsToShow & ItemElements::$Name) {
                $this->showName();
            }
            
            if ($elementsToShow & ItemElements::$Img) {
                $this->showImg();
            }
            
            if ($elementsToShow & ItemElements::$Description) {
                $this->showDescription();
            }
            
            $this->startMenu();
            
            if ($elementsToShow & ItemElements::$Price) {
                $this->showPrice();
            }
            
            if ($elementsToShow & ItemElements::$InfoBtn) {
                $this->showInfoBtn();
            }    
            
            if ($elementsToShow & ItemElements::$CartBtn) {
                $this->showCartBtn();
            }
            
            if ($elementsToShow & ItemElements::$Count) {
                $this->showCount();
            }
            
            $this->endMenu();
            
        }

        private function showName() {
            echo <<< EOL
            <div class="SaleItemHd">
                <a class="SaleItemHd" title="{$this->Name}" href="itemInfo.php?item_id={$this->Id}">
                    {$this->Name}
                </a>
            </div>
EOL;
        }
        
        private function showDescription() {
            printf('<div class="Info">%s</div>', 
                    $this->Description);
        }
        
        private function showImg() {
            printf('<a title="%s"> <img class="ItemImg" src="img/%s" > </a>', 
                    $this->Name, $this->ImgSrc);
        }
        
        private function startMenu() {
            echo '<div class="BuyerMenu"> ';
        }

        private function showCartBtn() {
            echo <<< EOL
                         
                <div class="addBasket priceContainer">
                    <form class="ButtonBasket" method="POST" action="orderInfo.php">
                        <input type="text" name="id" value="{$this->Id}" hidden="true"></input>
                        <input type="submit" value="В корзину"></input>
                    </form>
                </div>
                        
EOL;
        }
        
        private function showPrice() {
            echo <<< EOL
            <div class="priceContainer">
                    <div>
                        <span class="price"> {$this->Price} 
                            <span> р.</span> 
                        </span>

                    </div>
                    <div class="closer"></div>
            </div>
EOL;
        }
        
        private function showCount() {
            echo <<< EOL
                <div class="availableButton">
                    <div>
                        <span class="availabilityText"> На складе:  
                            <span> {$this->Count} шт.</span> 
                        </span>
                    </div>
                    <div class="closer"></div>
                </div>
EOL;
        }
        
        private function showInfoBtn() {
            echo <<< EOL
                <span class="addBasket priceContainer">
                          <span><a class="textInfo" href="itemInfo.php?item_id={$this->Id}" title="Подробнее">Подробнее</a></span>
                </span>
EOL;
        }
       
        private function endMenu() {
            echo '</div>';
        }
    }
?>