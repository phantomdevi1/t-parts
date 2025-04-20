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

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category_id = (int)$_POST['category'];
    $price = (float)$_POST['price'];
    $applicabilityArray = $_POST['applicability'];
  $applicability = implode(', ', $applicabilityArray);
    $availability = $_POST['availability'];
    $stock = (int)$_POST['stock'];
    $promotion = (int)$_POST['promotion'];

    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $image_tmp_name = $_FILES['image_path']['tmp_name'];
        $image_name = basename($_FILES['image_path']['name']);
        $upload_dir = 'img/categories/';
        $image_name = time() . "_" . basename($_FILES['image_path']['name']);
        $image_path = $upload_dir . $image_name;


        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($image_tmp_name, $image_path)) {
            $stmt = $conn->prepare("INSERT INTO parts (name, category_id, price, image_path, applicability, availability, stock, promotion)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sidsssii", $name, $category_id, $price, $image_path, $applicability, $availability, $stock, $promotion);

            if ($stmt->execute()) {
                $message = "<p style='color: green;'>✅ Запчасть успешно добавлена!</p>";
            } else {
                $message = "<p style='color: red;'>❌ Ошибка при добавлении: " . htmlspecialchars($stmt->error) . "</p>";
            }

            $stmt->close();
        } else {
            $message = "<p style='color: red;'>❌ Ошибка при загрузке изображения.</p>";
        }
    } else {
        $message = "<p style='color: red;'>❌ Пожалуйста, загрузите изображение.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Добавить запчасть</title>
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
<div class="content">
  <div class="container_heading_content">
          <a href="admin.php" class="back_catalog_btn">
              <img src="img/back.png" alt="">
              <span>Назад</span>
          </a>
          <h1>Добавление запчастей</h1>
          <?php if (!empty($message)) echo $message; ?>
      </div>
      <div class="admin_block">
      <form action="" method="post" enctype="multipart/form-data">
        <label for="category">Выберите категорию:</label>
        <select name="category" id="category" required>
          <?php
          $categories = $conn->query("SELECT id, name FROM categories");
          while ($category = $categories->fetch_assoc()) {
            echo "<option value='{$category['id']}'>{$category['name']}</option>";
          }
          ?>
        </select>
        <div class="form-group">
            <label for="name">Название запчасти:</label>
            <input type="text" name="name" id="name" required>
          </div>

        <div class="form-group">
            <label for="price">Цена:</label>
            <input type="number" name="price" id="price" step="0.01" required>
        </div>

        <div class="form-group">
            <label for="image_path">Изображение:</label>
            <input type="file" name="image_path" id="image_path" accept="image/*" required>
        </div>

        <div class="form-group">
            <label for="applicability">Применимость:</label>
            <div>
          <label><input type="checkbox" name="applicability[]" value="TANK 300"> TANK 300</label>
            </div>
            <div>
          <label><input type="checkbox" name="applicability[]" value="TANK 400"> TANK 400</label>
            </div>
            <div>
          <label><input type="checkbox" name="applicability[]" value="TANK 500"> TANK 500</label>
            </div>
            <div>
          <label><input type="checkbox" name="applicability[]" value="TANK 700"> TANK 700</label>
            </div>
        </div>

        <div class="form-group">
            <label for="availability">Наличие:</label>
            <select name="availability" id="availability" required>
          <option value="В наличии">В наличии</option>
          <option value="Под заказ">Под заказ</option>
            </select>
        </div>

        <div class="form-group">
            <label for="stock">Количество на складе:</label>
            <input type="number" name="stock" id="stock" required>
        </div>

        <div class="form-group">
            <label for="promotion">Акция:</label>
            <select name="promotion" id="promotion" required>
          <option value="0">Нет</option>
          <option value="1">Да</option>
            </select>
        </div>
        <div class="form-group">
          <button class="new_part_btn" type="submit">Добавить</button>
        </div>
      </form>
      </div>
</div>

</body>
</html>