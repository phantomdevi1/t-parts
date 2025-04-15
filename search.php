<?php
require 'config.php';
session_start();

$q = trim($_GET['q'] ?? '');

$search_results = [];

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("SELECT id, name, price, image_path, applicability, availability, stock FROM parts WHERE name LIKE ?");
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Handle adding to cart
$is_logged_in = isset($_SESSION['user_id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!$is_logged_in) {
        echo "<script>alert('Для добавления в корзину необходимо авторизоваться'); window.location.href='login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $part_id = (int)$_POST['part_id'];
    $quantity = (int)$_POST['quantity'];

    // Check if the part exists
    $sql_check_part = "SELECT id, stock FROM parts WHERE id = ?";
    $stmt = $conn->prepare($sql_check_part);
    $stmt->bind_param("i", $part_id);
    $stmt->execute();
    $part_result = $stmt->get_result()->fetch_assoc();

    if (!$part_result || $quantity > $part_result['stock']) {
        die("Ошибка: Недостаточно товара в наличии.");
    }

    // Add to cart
    $sql_cart = "INSERT INTO cart (user_id, part_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql_cart);
    $stmt->bind_param("iii", $user_id, $part_id, $quantity);
    $stmt->execute();
}

$cart_image = 'img/stroller.png';
if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $sql_cart_check = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql_cart_check);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result()->fetch_assoc();

    if ($cart_result['count'] > 0) {
        $cart_image = 'img/cart_full.png'; 
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результаты поиска</title>
    <link rel="stylesheet" href="style.css">
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
                        
                        while ($cat_row = $result->fetch_assoc()) {
                            $first_letter = mb_substr($cat_row['name'], 0, 1, 'UTF-8');
                            if (!isset($used_letters[$first_letter])) {
                                $selected_categories[] = $cat_row;
                                $used_letters[$first_letter] = true;
                            }
                            if (count($selected_categories) >= 5) {
                                break;
                            }
                        }
                        
                        foreach ($selected_categories as $cat) {
                            echo '<a href="categories.php?id=' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a>';
                        }
                        
                        ?>
                        <a href="catalog.php">Все категории</a>
                    </div>
            </div>

            <form method="get" action="search.php" class="search-form">
                <input type="search" name="q" class="search-input" placeholder="Название запчасти" value="<?= htmlspecialchars($q) ?>">
                <button type="submit" class="search-btn">Найти <img src="img/search.png" alt=""></button>
            </form>

            <a href="index.php#carsindex" class="icon-link"><img src="img/car.png" alt=""></a>
            <a href="cart.php" class="icon-link"><img src="<?= $cart_image ?>" alt=""></a>
            <a href="<?= $is_logged_in ? 'account.php' : 'login.php'; ?>" class="icon-link"><img src="img/profile_icon.png" alt=""></a>
        </div>
    </div>
</header>

<div class="content">
    <div class="container_heading_content">
        <p>Результаты поиска по запросу: "<?= htmlspecialchars($q) ?>"</p>
        <h1>Интернет-магазин T-PARTS</h1>
    </div>

      <?php if (empty($search_results)): ?>
    <p>Ничего не найдено.</p>
<?php else: ?>
    <table class="parts_table">
        <tbody>
            <?php foreach ($search_results as $part): ?>
              <tr>
                    <td><img src="<?= htmlspecialchars($part['image_path']) ?>" alt="<?= htmlspecialchars($part['name']) ?>" width="150"></td>
                    <td class="description_title_td"><?= htmlspecialchars($part['name']) ?>
                        <div class="description_td">
                            <p><?= htmlspecialchars($part['price']) ?> ₽</p>
                            <p><?= htmlspecialchars($part['applicability']) ?></p>
                        </div>
                    </td>
                    <td style="color: <?= $part['availability'] === 'В наличии' ? 'green' : 'red' ?>;">
                        <?= htmlspecialchars($part['availability']) ?>
                    </td>
                    <td>
                        <form method="post" class="categories_form">
                            <input type="hidden" name="part_id" value="<?= $part['id'] ?>">
                            <input class="numb_categories_input" type="number" name="quantity" min="1"
                                   max="<?= $part['stock'] > 0 ? $part['stock'] : 1 ?>" value="1" required>
                            <button type="submit" name="add_to_cart" class="add-to-cart">В корзину</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</div>

</body>
</html>
