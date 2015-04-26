<div class="headerPanel">
            <div class="container">
                <div class="logo"><a class="headerText" href="index.php">Солдатская удача</a>  </div>
                <!--noindex-->
                    <div class="headerPersonal">
                        <?php
                            include_once "Autentication/Auth.php";
                            include_once "Tools/User.php";
                            
                            $auth = AuthFabric::GetAuth();
                            
                            if ($auth->isAuth()) {
                                $user = $auth->getCurrentUser();
                                echo '<a class="headerLogin" href="userInfo.php">'.$user->getLogin().'</a>';
                                echo '<a class="headerLogin" href="Autentication/login.php?action=logout">Выход</a>';
                                if ($auth->isAdmin())
                                {
                                   echo '<a class="headerLogin" href="admin.php">Админ-меню</a>';
                                }
                            } else {
                                echo '<a class="headerLogin" onclick="Login()" >Вход</a>';                                
                            }
                            //<a class="headerLogin" onclick="closef()">Регистрация</a>
                        ?>
                    </div>
                <!--/noindex-->
                
                <div class="headerMenu">
                    <ul class="headerMenuList">
                        <li class="headerMenuItem">
                            <a class="headerText" title="На главную" href="index.php">На главную</a>                            
                        </li>
                        <?php
                            if ($auth->isAuth()) {
                                $actOrders = 'href="orders.php"';
                                $actCart = 'href="orderInfo.php"';
                            } else {
                                $actOrders = $actCart = 'onclick="Login()"';
                            }
                            echo <<< EOL
                            <li class="headerMenuItem">
                                <a class="headerText" title="Мои заказы" {$actOrders}>Мои заказы</a>  <!--≈сли пользователь не авторизован открыть окно авторизации-->
                            </li>
                            <li class="headerMenuItem">
                                <a class="headerText"  {$actCart}>Корзина</a>
                            </li>
EOL;
                        ?>
                    </ul>
                </div>
                <div class="closer"></div> 
            </div>   
</div>