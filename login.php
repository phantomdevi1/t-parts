<?php
require 'config.php';
session_start();

// Если пользователь уже авторизован, перенаправляем на главную страницу
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $username = $_POST['username'];  // Логин (может быть номером телефона или email)
    $password = $_POST['password'];  // Пароль

    // Проверка на пустые поля
    if (empty($username) || empty($password)) {
        $error_message = "Пожалуйста, введите логин и пароль.";
    } else {
        // Подготовка запроса для проверки логина и пароля
        $sql = "SELECT id, password FROM users WHERE phone = ? OR email = ?";
        $stmt = $conn->prepare($sql);

        // Важно: передаем два параметра в bind_param
        $stmt->bind_param("ss", $username, $username);  // "ss" — два строковых параметра

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Проверка пароля без хеширования
            if ($password === $user['password']) {
                // Если пароль верный, создаем сессию
                $_SESSION['user_id'] = $user['id'];
                header("Location: account.php"); // Перенаправление на главную страницу
                exit();
            } else {
                $error_message = "Неверный пароль.";
            }
        } else {
            $error_message = "Пользователь с таким логином не найден.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Авторизация</title>
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

            <a href="index.php#carsindex" class="icon-link"><img src="img/car.png" alt=""></a>
            <a href="cart.php" class="icon-link"><img src="img/stroller.png" alt=""></a>
            <a href="profile.php" class="icon-link"><img src="img/profile_icon.png" alt=""></a>
        </div>
    </div>
</header>

<div class="content">
    <div class="login_container">
        <h1>Авторизация</h1>
        <?php
        if (isset($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        ?>
    

    <form method="post" class="login-form">
        <input type="text" id="username" name="username" required placeholder="Логин">
        <input type="password" id="password" name="password" required placeholder="Пароль">

        <button class="login_btn" type="submit">Войти</button>
    </form>

    <p class="registr_login_block">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
</div>
</body>
</html>
