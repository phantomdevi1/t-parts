<?php
session_start();
require 'config.php';

// Проверка авторизации и прав
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

// Обработка удаления
if (isset($_POST['delete_id'])) {
    $delete_id = (int)$_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM parts WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
}

// Обновление данных
if (isset($_POST['update_id'])) {
    $update_id = (int)$_POST['update_id'];
    $price = (float)$_POST['price'];
    $availability = $_POST['availability'];
    $stock = (int)$_POST['stock'];
    $promotion = (int)$_POST['promotion'];

    $stmt = $conn->prepare("UPDATE parts SET price = ?, availability = ?, stock = ?, promotion = ? WHERE id = ?");
    $stmt->bind_param("dsiii", $price, $availability, $stock, $promotion, $update_id);
    $stmt->execute();
    $stmt->close();
}

// Получение всех запчастей
$parts = $conn->query("SELECT p.*, c.name AS category_name FROM parts p JOIN categories c ON p.category_id = c.id");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Все запчасти</title>
  <link rel="stylesheet" href="style.css">
  <link rel="shortcut icon" href="img/favicon.png">
  <style>
    table {
      width: 90%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: center;
    }
    img {
      max-width: 100px;
    }
    .form-inline {
      display: inline;
    }
  </style>
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

<div class="content">
  <div class="container_heading_content">
    <a href="admin.php" class="back_catalog_btn">
      <img src="img/back.png" alt="">
      <span>Назад</span>
    </a>
    <h1>Все запчасти</h1>
  </div>

  <table>
    <thead>
      <tr>
        <th>Название</th>
        <th>Категория</th>
        <th>Цена (₽)</th>
        <th>Применимость</th>
        <th>Наличие</th>
        <th>Склад</th>
        <th>Акция</th>
        <th>Действия</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($part = $parts->fetch_assoc()): ?>
        <tr>
          <form method="post" class="form-inline">
            <td><?= htmlspecialchars($part['name']) ?></td>
            <td><?= htmlspecialchars($part['category_name']) ?></td>
            <td><input type="number" step="0.01" name="price" value="<?= $part['price'] ?>"></td>
            <td><?= htmlspecialchars($part['applicability']) ?></td>
            <td>
              <select name="availability">
                <option value="В наличии" <?= $part['availability'] == 'В наличии' ? 'selected' : '' ?>>В наличии</option>
                <option value="Под заказ" <?= $part['availability'] == 'Под заказ' ? 'selected' : '' ?>>Под заказ</option>
              </select>
            </td>
            <td><input type="number" name="stock" value="<?= $part['stock'] ?>"></td>
            <td>
              <select name="promotion">
                <option value="1" <?= $part['promotion'] == 1 ? 'selected' : '' ?>>Да</option>
                <option value="0" <?= $part['promotion'] == 0 ? 'selected' : '' ?>>Нет</option>
              </select>
            </td>
            <td>
              <input type="hidden" name="update_id" value="<?= $part['id'] ?>">
              <button type="submit">💾</button>
          </form>
          <form method="post" onsubmit="return confirm('Вы уверены, что хотите удалить эту запчасть?');" class="form-inline">
            <input type="hidden" name="delete_id" value="<?= $part['id'] ?>">
            <button type="submit" style="color:red;">🗑️</button>
          </form>
            </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
