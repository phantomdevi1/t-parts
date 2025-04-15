<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
require 'config.php';
$cart_image = 'img/stroller.png'; // Значок по умолчанию

if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $sql_cart_check = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql_cart_check);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result()->fetch_assoc();

    if ($cart_result['count'] > 0) {
        $cart_image = 'img/cart_full.png'; // Изменённый значок
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
</head>
<body>
    <header>
        <div class="adress_header">
            <img src="img/geo1.png" alt="">
            <a href="https://yandex.ru/maps/10819/tver-oblast/house/torgovo_promyshlennaya_zona_borovlyovo_1_s4/Z0wYfwdnS0UAQFtsfXt4cHxjYA==/?ll=35.907207%2C56.791004&z=16" target="_blank">г.Тверь ТПЗ Боровлево-1 стр.4</a>
            <a href="tel:+7 (4822) 22-38-79" class="header_phone">+7 (4822) 22-38-79</a>
        </div>
       <div class="block_header_background">
        <div class="block_header">
            
            <a href="index.php" style="height: 40px;"><img src="img/favicon.png" alt="" class="logo_header"></a>
            <a href="index.php" class="text_logo">T-PARTS</a>

            <div class="catalog-container">
                <button class="catalog-btn">Каталог <img src="img/chevron-right.png" alt=""></button>
                    <div class="dropdown-menu">
                        <?php

                        $sql_categories = "SELECT id, name FROM categories ORDER BY name ASC";
                        $result = $conn->query($sql_categories);

                        $selected_categories = [];
                        $used_letters = [];

                        while ($category = $result->fetch_assoc()) {
                            $first_letter = mb_substr($category['name'], 0, 1, 'UTF-8');
                            if (!isset($used_letters[$first_letter])) {
                                $selected_categories[] = $category;
                                $used_letters[$first_letter] = true;
                            }
                            if (count($selected_categories) >= 5) {
                                break;
                            }
                        }

                        foreach ($selected_categories as $category) {
                            echo '<a href="categories.php?id=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a>';
                        }
                        ?>
                        <a href="catalog.php">Все категории</a>
                    </div>
            </div>



            <form action="search.php" method="get" class="search-form">
                <input type="search" class="search-input" name="q" placeholder="Наименование детали">
                <button type="submit" class="search-btn">Найти <img src="img/search.png" alt=""></button>
            </form>
            

            <a href="#carsindex" class="icon-link"><img src="img/car.png" alt=""></a>
            <a href="cart.php" class="icon-link"><img src="<?= $cart_image ?>" alt=""></a>
            <a href="<?php echo $is_logged_in ? 'account.php' : 'login.php'; ?>" class="icon-link"><img src="img/profile_icon.png" alt=""></a>
            </div>
        </div>
    </header>
    <div class="content">
        <div class="container_heading_content">
            <p>Главная</p>
            <h1>Интернет-магазин T-PARTS</h1>
        </div>

        <div class="collab_block">            
            <div class="text_collab_block">
                <p>TANK SERVICE</p>
                <span>профессиональный сервис официального обслуживания для вашего TANK</span>
                <a href="http://sfchizhmai.temp.swtest.ru" class="" target="_blank">ЗАПИСАТЬСЯ НА РЕМОНТ</a>
            </div>
        </div>

        <div class="intro_block">
            <div class="container_intro">
                <div class="text_intro">
                    <h2>ЗАПЧАСТИ У ВАС <br>
                        В КАРМАНЕ</h2>
                    <p>T-PARTS - лучший по подбору <br>
                        автозапчастей</p>
                </div>
                <div class="img_intro">
                    <img src="img/intro_img.png" alt="">
                </div>
            </div>            
        </div>

        <div class="popular_product_block">
            <p>Популярные категории</p>
            <div class="popular_product_container">
                <a href="categories.php?id=4" class="product_cart">
                    <img src="img/amort.png" alt="">
                    <p>Комплектующие подвески</p>
                </a>
                <div class="vertical"></div>
                <a href="categories.php?id=8" class="product_cart">
                    <img src="img/antifriz.png" alt="">
                    <p>Автомобильные жидкости</p>
                </a>
                <div class="vertical"></div>
                <a href="categories.php?id=3" class="product_cart">
                    <img src="img/disk.png" alt="">
                    <p>Тормозная система</p>
                </a>
                <div class="vertical"></div>
                <a href="categories.php?id=11" class="product_cart">
                    <img src="img/accum.png" alt="">
                    <p>Автомобильные аккумуляторы</p>
                </a>
            </div>
        </div>


        <a href="categories.php?id=8" class="action_block">
            <img src="img/oil.png" alt="">
            <div class="action_text_block">
                <h3>АКЦИЯ!</h3>
                <p class="action_name">Моторное масло</p>
                <p class="action_description">Объем: 5,6л</p>
                <p class="action_price">Цена: <span>5000</span></p>                
            </div>
        </a>

        <div class="present_block">
            <div class="first_line">
                <img src="img/т300i.png" alt="" class="present_wowimg">
                <div class="first_line_car" id="carsindex">
                    <p>Автомобили</p>
                    <hr class="hr_first_car">
                    <a href="model_parts.php?model=TANK%20300">TANK 300</a>
                    <hr class="hr_first_car">
                    <a href="model_parts.php?model=TANK%20500">TANK 500</a>
                    <hr class="hr_first_car">
                    <a href="model_parts.php?model=TANK%20700">TANK 700</a>
                    <hr class="hr_first_car">
                    <a href="model_parts.php?model=TANK%20400">TANK 400</a>
                </div>

                <a href="categories.php?id=7" class="present_product_block">
                    <img src="img/dopimg.png" alt="">
                    <p>Акссесуары</p>
                </a>
            </div>
            <img class="present_wowbigimg" src="img/tank500.png" alt="">
            <div class="second_line">
                <a href="categories.php?id=6" class="second_line_first">
                    <img src="img/lamp.png" alt="">
                    <p>Лампочки</p>
                </a>
                <a href="categories.php?id=10" class="second_line_second">
                    <img src="img/hotwheal.png" alt="" class="">
                    <p>Литые диски</p>
                </a>
                <a href="categories.php?id=12" class="second_line_third">
                    <img src="img/wheels.png" alt="" >
                    <p>Автомобильная резина</p>
                </a>
            </div>
           
                <img src="img/stocks.png" alt="" width="80%"style="margin-top: 50px; margin-bottom: 50px">
           
            <div class="second_line">
               
                    <img src="img/t300.png" alt="" class="present_wowimg">

                    <img src="img/т500.png" alt="" class="present_wowimg_center">

                    <img src="img/t700.png" alt="" class="present_wowimg_right">
                
                
            </div>
        </div>

        <?php
            include 'config.php';

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT title, content, DATE_FORMAT(created_at, '%d.%m.%Y') as formatted_date FROM news ORDER BY created_at DESC LIMIT 4";
            $result = $conn->query($sql);
            ?>

            <div class="news_index">
                <p class="news_title">Новости</p>
                <div class="news_block">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="one_news_block">
                            <p class="one_news_block-title"><?php echo htmlspecialchars($row['title']); ?></p>
                            <p class="one_news_block-date"><?php echo $row['formatted_date']; ?></p>
                            <p class="one_news_block-text"><?php echo htmlspecialchars($row['content']); ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

        <?php $conn->close(); ?>

    </div>
    <footer>
        <div class="footer_block">
            <div class="footer_info_block">
                <p class="footer_title">Адрес:</p>
                <p class="footer_text">Тверская область, Калининский муниципальный округ, торгово-промышленная зона Боровлёво-1, с4</p>

                <p class="footer_title">Номер телефона:</p>
                <p class="footer_text">
                    +7 909 267 0401
                    <br>
                    +7 909 267 0402
                    <br>
                    +7 909 267 0403
                    <br>
                    +7 (4822) 22-38-79
                </p>

                <p class="footer_title">График работы:</p>
                <p class="footer_text">
                    ПН-ВС: <br>
                    09:00–21:00
                </p>
            </div>
            <div class="footer_href_block">
                <p class="footer_title">Полезные ссылки:</p>
                <a href="https://autopremium-tank.ru" class="footer_text">Официальный дилер TANK в Твери</a>
                <a href="https://tank.ru" class="footer_text">Офицальный сайт TANK</a>
                <a href="#" class="footer_text">Автосервис TANK</a>
            </div>
            <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Abdf9bd6a194711bfb99b10395e8f217040bf9db9dbcc46aad19420ac28304fff&amp;source=constructor" class="index_map" frameborder="0"></iframe>
        </div>
    </footer>
</body>

</html>