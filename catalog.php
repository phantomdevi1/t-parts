<?php
require 'config.php'; // Подключение к БД

$sql = "SELECT id, name, image_path FROM categories ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог</title>
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
            <a href="index.php" class="text_logo">T-PARTS</a>

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
        <p>Все категории</p>
        <h1>Интернет-магазин T-PARTS</h1>
    </div>
    <div class="catalog_container">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="catalog_block">
          <a href="category.php?id=<?= $row['id'] ?>" class="product_cart">
            <img src="<?= $row['image_path'] ?>" alt="<?= htmlspecialchars($row['name']) ?>">
            <p><?= htmlspecialchars($row['name']) ?></p>
          </a>
        </div>
      <?php endwhile; ?>
    </div>
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
