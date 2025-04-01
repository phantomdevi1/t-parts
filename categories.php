<?php
require 'config.php'; // Подключение к БД

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем название категории
$category_sql = "SELECT name FROM categories WHERE id = ?";
$stmt = $conn->prepare($category_sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$category_result = $stmt->get_result();
$category = $category_result->fetch_assoc();

// Получаем список запчастей
$parts_sql = "SELECT name, price, image_path, applicability, availability FROM parts WHERE category_id = ?";
$stmt = $conn->prepare($parts_sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$parts_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Категория</title>
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
                        <?php
                        require 'config.php';

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

            <a href="#" class="icon-link"><img src="img/car.png" alt=""></a>
            <a href="#" class="icon-link"><img src="img/stroller.png" alt=""></a>
            </div>
        </div>
  </header>

  <div class="content">
    <div class="container_heading_content">
        <p><?= htmlspecialchars($category['name'] ?? 'Категория не найдена') ?></p> 
        <h1>Интернет-магазин T-PARTS</h1>
    </div>

    <table class="parts_table">
        <tbody>
            <?php while ($part = $parts_result->fetch_assoc()): ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($part['image_path']) ?>" alt="<?= htmlspecialchars($part['name']) ?>" width="50"></td>
                    <td><?= htmlspecialchars($part['name']) ?></td>
                    <td><?= htmlspecialchars($part['price']) ?> ₽</td>
                    <td><?= htmlspecialchars($part['applicability']) ?></td>
                    <td style="color: <?= $part['availability'] === 'В наличии' ? 'green' : 'red' ?>;">
                        <?= htmlspecialchars($part['availability']) ?>
                    </td>
                    <td><button class="add-to-cart">Добавить в корзину</button></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
  </div>
</body>
</html>