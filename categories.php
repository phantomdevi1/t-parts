<?php
require 'config.php';
session_start();

$is_logged_in = isset($_SESSION['user_id']);
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем название категории
$category_sql = "SELECT name FROM categories WHERE id = ?";
$stmt = $conn->prepare($category_sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$category_result = $stmt->get_result();
$category = $category_result->fetch_assoc();

// Обработка добавления в корзину
$error_message = '';
$success_part_id = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!$is_logged_in) {
        echo "<script>alert('Для добавления в корзину необходимо авторизоваться'); window.location.href='login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $part_id = (int)$_POST['part_id'];
    $quantity = (int)$_POST['quantity'];

    $sql_stock = "SELECT stock FROM parts WHERE id = ?";
    $stmt = $conn->prepare($sql_stock);
    $stmt->bind_param("i", $part_id);
    $stmt->execute();
    $stock_result = $stmt->get_result()->fetch_assoc();

    if ($quantity > $stock_result['stock']) {
        $error_message = "Нельзя заказать больше, чем есть на складе.";
    } else {
        $check_cart = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND part_id = ?");
        $check_cart->bind_param("ii", $user_id, $part_id);
        $check_cart->execute();
        $existing = $check_cart->get_result()->fetch_assoc();

        if (!$existing) {
            $sql_cart = "INSERT INTO cart (user_id, part_id, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql_cart);
            $stmt->bind_param("iii", $user_id, $part_id, $quantity);
            $stmt->execute();
            $success_part_id = $part_id;
        } else {
            $error_message = "Этот товар уже в корзине.";
        }
    }
}

// Получаем список запчастей
$parts_sql = "SELECT id, name, price, image_path, applicability, availability, stock, promotion FROM parts WHERE category_id = ?";
$stmt = $conn->prepare($parts_sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$parts_result = $stmt->get_result();

// Получаем содержимое корзины
$parts_in_cart = [];

if ($is_logged_in) {
    $sql = "SELECT part_id, quantity FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $parts_in_cart[$row['part_id']] = $row['quantity'];
    }
}

// Значок корзины
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
    <title>Категория</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
</head>
<body>
<header>
        <div class="adress_header">
            <img src="img/geo1.png" alt="">
            <a href="https://yandex.ru/maps/10819/tver-oblast/house/torgovo_promyshlennaya_zona_borovlyovo_1_s4/Z0wYfwdnS0UAQFtsfXt4cHxjYA==/?ll=35.907207%2C56.791004&z=16" target="_blank">г.Тверь ТПЗ Боровлево-1 стр.4</a>
            <a href="tel:+7(4822)79-79-97" class="header_phone">+7 (4822) 79-79-97</a>
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

                    while ($dropdown_category = $result->fetch_assoc()) {
                        $first_letter = mb_substr($dropdown_category['name'], 0, 1, 'UTF-8');
                        if (!isset($used_letters[$first_letter])) {
                            $selected_categories[] = $dropdown_category;
                            $used_letters[$first_letter] = true;
                        }
                        if (count($selected_categories) >= 5) break;
                    }
                    
                    foreach ($selected_categories as $cat) {
                        echo '<a href="categories.php?id=' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a>';
                    }
                    ?>
                    <a href="catalog.php">Все категории</a>
                </div>
            </div>

            <form action="search.php" method="get" class="search-form">
                <input type="search" class="search-input" name="q" placeholder="Наименование детали">
                <button type="submit" class="search-btn">Найти <img src="img/search.png" alt=""></button>
            </form>

            <a href="index.php#carsindex" class="icon-link"><img src="img/car.png" alt=""></a>
            <a href="cart.php" class="icon-link"><img src="<?= $cart_image ?>" alt=""></a>
            <a href="<?= $is_logged_in ? 'account.php' : 'login.php' ?>" class="icon-link"><img src="img/profile_icon.png" alt=""></a>
        </div>
    </div>
</header>

<div class="content">
    <div class="container_heading_content">
        <a href="catalog.php" class="back_catalog_btn">
            <img src="img/back.png" alt="">
            <span>Назад</span>
        </a>
        <p><?= htmlspecialchars($category['name'] ?? 'Категория не найдена') ?></p>
        <h1>Интернет-магазин T-PARTS</h1>

        <?php if (!empty($error_message)): ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
    </div>

    <table class="parts_table">
        <tbody>
        <?php while ($part = $parts_result->fetch_assoc()): ?>
            <tr>
                <td><img src="<?= htmlspecialchars($part['image_path']) ?>" alt="<?= htmlspecialchars($part['name']) ?>" width="150"></td>
                <td class="description_title_td"><?= htmlspecialchars($part['name']) ?>
                    <div class="description_td">
                        <p><?= htmlspecialchars($part['applicability']) ?></p>
                    </div>
                </td>
                <td style="color: <?= $part['availability'] === 'В наличии' ? 'green' : 'red' ?>;">
                    <?= htmlspecialchars($part['availability']) ?>
                </td>
                <td style="text-align: end;">
                <?php if (array_key_exists($part['id'], $parts_in_cart)): ?>
                    <div class="categories_form_inline">                        
                    <input class="numb_categories_input" type="number" name="quantity" min="1"
                            max="<?= $part['stock'] > 0 ? $part['stock'] : 1 ?>" value="1" required>
                        <p class="price <?= $part['promotion'] ? 'promo-price' : '' ?>">
                            <?= number_format($part['price'], 2, '.', ' ') ?> ₽
                        </p>
                        <button class="in-cart-btn" disabled>В корзине</button>
                    </div>
                <?php else: ?>
                    <form method="post" class="categories_form_inline" onsubmit="return validateStock(this, <?= $part['stock'] ?>)">
                        <input type="hidden" name="part_id" value="<?= $part['id'] ?>">
                        <input class="numb_categories_input" type="number" name="quantity" min="1" value="1" required>
                        <p class="price <?= $part['promotion'] ? 'promo-price' : '' ?>">
                            <?= number_format($part['price'], 2, '.', ' ') ?> ₽
                        </p>
                        <button type="submit" name="add_to_cart" class="add-to-cart">В корзину</button>
                    </form>

                <?php endif; ?>
                </td>


            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script>
function validateStock(form, stock) {
    const quantity = parseInt(form.quantity.value);
    if (quantity > stock) {
        alert("Нельзя заказать больше, чем есть на складе");
        return false; // Остановить отправку формы
    }
    return true;
}
</script>

</body>
</html>
