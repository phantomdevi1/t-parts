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
            <a href="#">г.Тверь ТПЗ Боровлево-1 стр.4</a>
        </div>
       <div class="block_header_background">
        <div class="block_header">
            
            <img src="img/favicon.png" alt="" class="logo_header">
            <a href="#" class="text_logo">T-PARTS</a>

            <div class="catalog-container">
                <button class="catalog-btn">Каталог <img src="img/chevron-right.png" alt=""></button>
                <div class="dropdown-menu">
                    <a href="#">Двигатель</a>
                    <a href="#">Трансмиссия</a>
                    <a href="#">Ходовая часть</a>
                    <a href="#">Тормозная система</a>
                    <a href="#">Электрооборудование</a>
                    <a href="catalog.php">Все категории</a>
                </div>
            </div>

            <input type="search" class="search-input" placeholder="Артикул или номер детали">
            <button type="submit" class="search-btn">
                Найти <img src="img/search.png" alt="">
            </button>
            

            <a href="#" class="icon-link"><img src="img/car.png" alt=""></a>
            <a href="#" class="icon-link"><img src="img/stroller.png" alt=""></a>
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
                <a href="" class="">ЗАПИСАТЬСЯ НА РЕМОНТ</a>
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
            <p>популярные категории</p>
            <div class="popular_product_container">
                <a href="suspension.html" class="product_cart">
                    <img src="img/amort.png" alt="">
                    <p>Комплектующие подвески</p>
                </a>
                <div class="vertical"></div>
                <a href="fluids.html" class="product_cart">
                    <img src="img/antifriz.png" alt="">
                    <p>Автомобильные жидкости</p>
                </a>
                <div class="vertical"></div>
                <a href="brakes.html" class="product_cart">
                    <img src="img/disk.png" alt="">
                    <p>Тормозная система</p>
                </a>
                <div class="vertical"></div>
                <a href="batteries.html" class="product_cart">
                    <img src="img/accum.png" alt="">
                    <p>Автомобильные аккумуляторы</p>
                </a>
            </div>
            
        </div>

        <div class="action_block">
            <img src="img/oil.png" alt="">
            <div class="action_text_block">
                <h3>АКЦИЯ!</h3>
                <p class="action_name">Моторное масло</p>
                <p class="action_description">Объем: 5,6л</p>
                <p class="action_price">Цена: <span>5000</span></p>                
            </div>
        </div>

        <div class="present_block">
            <div class="first_line">
                <img src="img/т300i.png" alt="" class="present_wowimg">
                <div class="first_line_car">
                    <p>Автомобили</p>
                    <hr class="hr_first_car">
                    <a href="">TANK 300</a>
                    <hr class="hr_first_car">
                    <a href="">TANK 500</a>
                    <hr class="hr_first_car">
                    <a href="">TANK 700</a>
                    <hr class="hr_first_car">
                    <a href="">TANK 400</a>
                </div>
                <a href="" class="present_product_block">
                    <img src="img/dopimg.png" alt="">
                    <p>Акссесуары</p>
                </a>
            </div>
            <img class="present_wowbigimg" src="img/tank500.png" alt="">
            <div class="second_line">
                <a href="" class="second_line_first">
                    <img src="img/lamp.png" alt="">
                    <p>Лампочки</p>
                </a>
                <a href="" class="second_line_second">
                    <img src="img/hotwheal.png" alt="" class="">
                    <p>Литые диски</p>
                </a>
                <a href="" class="second_line_third">
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