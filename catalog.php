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
    <title>Каталог</title>
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
            </button>
            

            <a href="index.php#carsindex" class="icon-link"><img src="img/car.png" alt=""></a>
            <a href="cart.php" class="icon-link"><img src="<?= $cart_image ?>" alt=""></a>
            <a href="<?php echo $is_logged_in ? 'account.php' : 'login.php'; ?>" class="icon-link"><img src="img/profile_icon.png" alt=""></a>
            </div>
        </div>
    </header>

  <div class="content">
    <div class="container_heading_content">
        <p>Все категории</p>
        <h1>Интернет-магазин T-PARTS</h1>
    </div>
    <div class="catalog_container">
    <?php
      require 'config.php'; // Подключение к БД

      $sql = "SELECT id, name, image_path FROM categories ORDER BY name ASC";
      $result = $conn->query($sql);
    ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="catalog_block">
          <a href="categories.php?id=<?= $row['id'] ?>" class="product_cart">
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
                <a href="https://autopremium-tank.ru" class="footer_text" target="_blank">Официальный дилер TANK в Твери</a>
                <a href="https://tank.ru" class="footer_text" target="_blank">Офицальный сайт TANK</a>
                <a href="http://sfchizhmai.temp.swtest.ru" class="footer_text" target="_blank">Автосервис TANK</a>
            </div>
            <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Abdf9bd6a194711bfb99b10395e8f217040bf9db9dbcc46aad19420ac28304fff&amp;source=constructor" class="index_map" frameborder="0"></iframe>
        </div>
    </footer>
</body>
</html>
