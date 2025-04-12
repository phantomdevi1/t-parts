<?php
require 'config.php';
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- Оформление заказа при POST-запросе ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    // Получаем корзину
    $sql = "SELECT part_id, quantity FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart_data = [];
    $total_price = 0;

    // Проходим по товарам в корзине и вычисляем общую стоимость
    while ($row = $result->fetch_assoc()) {
        $part_id = $row['part_id'];
        $quantity = $row['quantity'];
        
        // Получаем цену каждой детали
        $part_query = $conn->prepare("SELECT price FROM parts WHERE id = ?");
        $part_query->bind_param("i", $part_id);
        $part_query->execute();
        $part_result = $part_query->get_result()->fetch_assoc();
        
        if ($part_result) {
            $price = $part_result['price'];
            $total_price += $price * $quantity;

            $cart_data[] = [
                'part_id' => $part_id,
                'quantity' => $quantity,
                'price' => $price
            ];
        } else {
            die("Ошибка: Деталь с ID $part_id не найдена в таблице parts.");
        }
    }

    if (!empty($cart_data)) {
        // Вставка заказа в таблицу orders
        $status = 'В обработке';
        $order_sql = "INSERT INTO orders (user_id, status, total_price, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($order_sql);
        $stmt->bind_param("isd", $user_id, $status, $total_price);
        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;

            // Вставка позиций в таблицу order_items
            $item_sql = "INSERT INTO order_items (order_id, part_id, quantity, price) VALUES (?, ?, ?, ?)";
            $item_stmt = $conn->prepare($item_sql);
            foreach ($cart_data as $item) {
                $item_stmt->bind_param("iiid", $order_id, $item['part_id'], $item['quantity'], $item['price']);
                $item_stmt->execute();
            }
            exit();      
             // Очистка корзины пользователя
            $delete_sql = "DELETE FROM cart WHERE user_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $user_id);
            $delete_stmt->execute();

            // Перезагружаем страницу после оформления заказа
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
            } else {
            die("Ошибка оформления заказа: " . $stmt->error);
            }
            } else {
            die("Корзина пуста. Невозможно оформить заказ.");
            }     
}

// Получение товаров из корзины для отображения
$sql = "SELECT c.id AS cart_id, p.id AS part_id, p.name, p.price, p.image_path, c.quantity
        FROM cart c
        JOIN parts p ON c.part_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Корзина</title>
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
                        if (count($selected_categories) >= 5) break;
                    }

                    foreach ($selected_categories as $category) {
                        echo '<a href="categories.php?id=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a>';
                    }
                    ?>
                    <a href="catalog.php">Все категории</a>
                </div>
            </div>

            <input type="search" class="search-input" placeholder="Артикул или номер детали">
            <button type="submit" class="search-btn">Найти <img src="img/search.png" alt=""></button>
            <a href="index.php#carsindex" class="icon-link"><img src="img/car.png" alt=""></a>
            <a href="#" class="icon-link"><img src="img/stroller.png" alt=""></a>
            <a href="account.php" class="icon-link"><img src="img/profile_icon.png" alt=""></a>
        </div>
    </div>
</header>

<div class="container_cart">
    <h1>Корзина</h1>

    <?php if (count($cart_items) === 0): ?>
        <p>Ваша корзина пуста.</p>
    <?php else: ?>
        <table class="parts_table">
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($item['image_path']) ?>" alt="" width="150"></td>
                        <td class="description_title_td">
                          <?= htmlspecialchars($item['name']) ?>
                          <div class="description_td">
                            <p><?= htmlspecialchars($item['price']) ?> ₽</p>
                          </div>
                        </td>
                        <td><?= htmlspecialchars($item['quantity']) ?> шт.</td>
                        <td><?= $item['price'] * $item['quantity'] ?> ₽</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Итого: <?= $total ?> ₽</h3>
        <form method="post">
            <button type="submit" name="checkout" class="add_cart_btn">Оформить заказ</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
