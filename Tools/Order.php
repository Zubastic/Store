<?php
    class Order {

        public function __construct($array) {
            $db = DBWorkerFabric::GetDataBaseWorker();
            
            $this->Number = $array['number'];
            $this->ItemList = array();
            foreach ($array['itemList'] as $id) {
                $this->ItemList[] = $db->getItem($id);
            }
            
            // TODO: Сделать нормальную дату (DateTime).
            $this->Date = $array['date'];
        }

        public $Number = 0;
        public $ItemList = array();
        public $Date = null;
    }
?>