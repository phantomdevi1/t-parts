<?php
session_start();
require 'config.php';

// Проверка авторизации и прав
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Выход
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Получаем имя администратора
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Добавление новости
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['news_title'], $_POST['news_content'])) {
    $title = $_POST['news_title'];
    $content = $_POST['news_content'];
    $created_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO news (title, content, created_at) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $content, $created_at);
    $stmt->execute();
}

// Обновление статуса заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
}

// Кол-во заказов "В обработке"
$pending = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status = 'В обработке'")
                ->fetch_assoc()['total'];

// Самый популярный товар текущего месяца
$current_month = date('Y-m');
$stmt = $conn->prepare("
    SELECT p.name, SUM(oi.quantity) AS total_quantity
    FROM order_items oi
    JOIN parts p ON p.id = oi.part_id
    JOIN orders o ON o.id = oi.order_id
    WHERE DATE_FORMAT(o.created_at, '%Y-%m') = ?
    GROUP BY oi.part_id
    ORDER BY total_quantity DESC
    LIMIT 1
");
$stmt->bind_param("s", $current_month);
$stmt->execute();
$top_product = $stmt->get_result()->fetch_assoc();

// Все заказы
$orders = $conn->query("
    SELECT orders.id, users.username, status, total_price, created_at
    FROM orders
    JOIN users ON orders.user_id = users.id
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Панель администратора</title>
  <link rel="stylesheet" href="style.css">
  <link rel="shortcut icon" href="img/favicon.png">
</head>
<body>
<header>
  <div class="adress_header">
    <img src="img/geo1.png" alt="">
    <a href="#">г.Тверь ТПЗ Боровлево-1 стр.4</a>
    <a href="tel:+7 (4822) 22-38-79" class="header_phone">+7 (4822) 22-38-79</a>
  </div>
  <div class="block_header_background">
    <div class="block_header">
      <p class="admin_text_logo">T-PARTS администратор</p>
      <a class="href_admin_account" href="?logout=1">Выйти</a>
    </div>
  </div>
</header>

<div class="content_account">
  <div class="admin_block">
    <h1>Панель администратора</h1>
    <p><strong>Добро пожаловать, администратор</strong> <?= htmlspecialchars($user['username']) ?></p>
    <p><strong>Количество заказов в обработке:</strong> <?= $pending ?></p>

    <?php if ($top_product): ?>
        <p><strong>Самый популярный товар в этом месяце:</strong> <?= htmlspecialchars($top_product['name']) ?> (<?= $top_product['total_quantity'] ?> шт)</p>
    <?php endif; ?>

    <h2>Добавить новость</h2>
    <form method="post" class="add_news_form">
      <input type="text" name="add_news_title" placeholder="Заголовок" required>
      <textarea name="add_news_content" placeholder="Текст новости" required></textarea>
      <button type="submit">Добавить</button>
    </form>
  </div>

  <div class="user_orders">
    <h2>Все заказы</h2>
    <table class="table_account">
      <thead>
        <tr>
          <th>ID</th>
          <th>Пользователь</th>
          <th>Дата</th>
          <th>Статус</th>
          <th>Сумма</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($order = $orders->fetch_assoc()): ?>
          <tr>
            <form method="post">
              <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
              <input type="hidden" name="update_status" value="1">
              <td><?= $order['id'] ?></td>
              <td><?= htmlspecialchars($order['username']) ?></td>
              <td><?= $order['created_at'] ?></td>
              <td>
                <select name="status">
                  <?php
                  $statuses = ['В обработке', 'Подтверждён', 'В доставке', 'Доставлено', 'Отменено', 'Выдано'];
                  foreach ($statuses as $status) {
                      $selected = $order['status'] === $status ? 'selected' : '';
                      echo "<option value=\"$status\" $selected>$status</option>";
                  }
                  ?>
                </select>
              </td>
              <td><?= $order['total_price'] ?> ₽</td>
              <td><button type="submit">Обновить</button></td>
            </form>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
