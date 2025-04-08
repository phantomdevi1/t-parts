<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
require 'config.php';

if (isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  header("Location: index.php");
  exit();
}
if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Профиль</title>
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



            <input type="search" class="search-input" placeholder="Артикул или номер детали">
            <button type="submit" class="search-btn">
                Найти <img src="img/search.png" alt="">
            </button>
            

            <a href="#carsindex" class="icon-link"><img src="img/car.png" alt=""></a>
            <a href="#" class="icon-link"><img src="img/stroller.png" alt=""></a>
            <a href="<?php echo $is_logged_in ? 'account.php' : 'login.php'; ?>" class="icon-link"><img src="img/profile_icon.png" alt=""></a>
            </div>
        </div>
    </header>

    <a class="href_personal_account" href="?logout=1">выйти</a>
</body>
</html>