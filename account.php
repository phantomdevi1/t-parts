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

    // Получение информации о пользователе
    $sql_user = "SELECT username, email, phone FROM users WHERE id = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();
    $user_info = $user_result->fetch_assoc();

    // Получение количества заказов пользователя
    $sql_orders_count = "SELECT COUNT(*) AS order_count FROM orders WHERE user_id = ?";
    $stmt_orders_count = $conn->prepare($sql_orders_count);
    $stmt_orders_count->bind_param("i", $user_id);
    $stmt_orders_count->execute();
    $orders_count_result = $stmt_orders_count->get_result();
    $orders_count = $orders_count_result->fetch_assoc()['order_count'];

    // Получение всех заказов пользователя
    $sql_orders = "SELECT id, status, total_price, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC";
    $stmt_orders = $conn->prepare($sql_orders);
    $stmt_orders->bind_param("i", $user_id);
    $stmt_orders->execute();
    $orders_result = $stmt_orders->get_result();
}
?>

<!DOCTYPE html>
<html lang="ru">
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
      <button type="submit" class="search-btn">
        Найти <img src="img/search.png" alt="">
      </button>
      <a href="index.php#carsindex" class="icon-link"><img src="img/car.png" alt=""></a>
      <a href="cart.php" class="icon-link"><img src="img/stroller.png" alt=""></a>
      <a href="<?php echo $is_logged_in ? 'account.php' : 'login.php'; ?>" class="icon-link"><img src="img/profile_icon.png" alt=""></a>
    </div>
  </div>
</header>

<div class="content_account">
  <div class="user_block">
    <h1>Личный кабинет</h1>
    <div class="user_info">
      <p><strong>Имя:</strong> <?= htmlspecialchars($user_info['username']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($user_info['email']) ?></p>
      <p><strong>Телефон:</strong> <?= htmlspecialchars($user_info['phone']) ?></p>
      <p><strong>Количество заказов:</strong> <?= $orders_count ?></p>
    </div>

    <a class="href_personal_account" href="?logout=1">Выйти</a>
  </div>

  <div class="user_orders">
    <h2>Мои заказы</h2>

    <?php if ($orders_count > 0): ?>
      <table class="table_account">
        <thead>
          <tr>
            <th>Номер заказа</th>
            <th>Дата</th>
            <th>Статус</th>
            <th>Сумма</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($order = $orders_result->fetch_assoc()): ?>
            <tr>
              <td><?= $order['id'] ?></td>
              <td><?= $order['created_at'] ?></td>
              <td><?= $order['status'] ?></td>
              <td><?= $order['total_price'] ?> ₽</td>
              <td><button onclick="toggleOrderDetails(<?= $order['id'] ?>)">Показать состав</button></td>
            </tr>
            <tr id="order-details-<?= $order['id'] ?>" style="display: none;">
              <td colspan="5">
                <div class="order-summary">
                  <h4>Состав заказа:</h4>
                  <table class="order-summary-table">
                    <thead>
                      <tr>
                        <th>Товар</th>
                        <th>Количество</th>
                        <th>Цена за шт.</th>
                        <th>Сумма</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      // Получаем товары в заказе
                      $sql_order_items = "SELECT oi.quantity, oi.price, p.name FROM order_items oi
                                          JOIN parts p ON oi.part_id = p.id
                                          WHERE oi.order_id = ?";
                      $stmt_order_items = $conn->prepare($sql_order_items);
                      $stmt_order_items->bind_param("i", $order['id']);
                      $stmt_order_items->execute();
                      $order_items_result = $stmt_order_items->get_result();

                      while ($order_item = $order_items_result->fetch_assoc()):
                      ?>
                        <tr>
                          <td><?= htmlspecialchars($order_item['name']) ?></td>
                          <td><?= $order_item['quantity'] ?></td>
                          <td><?= $order_item['price'] ?> ₽</td>
                          <td><?= $order_item['quantity'] * $order_item['price'] ?> ₽</td>
                        </tr>
                      <?php endwhile; ?>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>У вас нет заказов.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  // Функция для отображения/скрытия состава заказа
  function toggleOrderDetails(orderId) {
    var detailsRow = document.getElementById("order-details-" + orderId);
    if (detailsRow.style.display === "none" || detailsRow.style.display === "") {
      detailsRow.style.display = "table-row";
    } else {
      detailsRow.style.display = "none";
    }
  }
</script>

</body>
</html>
