<?php
    include_once 'DataBase.php';
    //include_once 'Item.php';
    
    echo '<div class="container positioner">';
        echo '<ul class="CatalogList">';

            // Массив придет из БД.
            $items = DBWorkerFabric::GetDataBaseWorker()->getCategories();

            for($i = 0; $i < count($items); $i++) {
                $ind = $i + 1;
                echo <<< EOL
                    <li class="CatalogItem HeaderCatalog">
                        <a class="CatalogItemLink" href="index.php?category={$ind}"> 
                            <span class="LeftBoardtext">
                                {$items[$i]}
                            </span>
                        </a>  
                    </li>
EOL;
            }
        echo '</ul>';
    echo '</div>';

?>