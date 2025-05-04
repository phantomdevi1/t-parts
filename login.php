<?php
require 'config.php';
session_start();


if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Проверка на пустые поля
    if (empty($username) || empty($password)) {
        $error_message = "Пожалуйста, введите логин и пароль.";
    } else {
        $sql = "SELECT id, password, is_admin FROM users WHERE phone = ? OR email = ?";
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ss", $username, $username); 

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['is_admin'] = $user['is_admin'];
            
                if ($user['is_admin'] == 1) {
                    header("Location: admin.php");
                } else {
                    header("Location: account.php");
                }
                exit();
            }
             else {
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
            <a href="https://yandex.ru/maps/10819/tver-oblast/house/torgovo_promyshlennaya_zona_borovlyovo_1_s4/Z0wYfwdnS0UAQFtsfXt4cHxjYA==/?ll=35.907207%2C56.791004&z=16" target="_blank">г.Тверь ТПЗ Боровлево-1 стр.4</a>
            <a href="tel:+7(4822)79-79-97" class="header_phone">+7 (4822) 79-79-97</a>
        </div>
       <div class="block_header_background">
        <div class="block_header">
            
            <a href="index.php" class="logo_header_href" style="height: 40px;"><img src="img/favicon.png" alt="" class="logo_header"></a>
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



            <form action="search.php" method="get" class="search-form">
                <input type="search" class="search-input" name="q" placeholder="Наименование детали">
                <button type="submit" class="search-btn">Найти <img src="img/search.png" alt=""></button>
            </form>
            

            <a href="index.php#carsindex" class="icon-link"><img src="img/car.png" alt=""></a>
            <a href="cart.php" class="icon-link"><img src="img/stroller.png" alt=""></a>
            <a href="<?= isset($_SESSION['user_id']) ? ($_SESSION['is_admin'] == 1 ? 'admin.php' : 'account.php') : 'login.php'; ?>" class="icon-link"><img src="img/profile_icon.png" alt=""></a>

            </div>
        </div>
    </header>

<div class="content">
    <div class="login_container">
        <h1>Авторизация</h1>
        <?php
        if (isset($error_message)) {
            echo "<p style='color: white;'>$error_message</p>";
        }
        ?>
    

    <form method="post" class="login-form">
        <input type="text" id="username" name="username" required placeholder="E-mail или телефон">
        <input type="password" id="password" name="password" required placeholder="Пароль">

        <button class="login_btn" type="submit">Войти</button>
    </form>

    <p class="registr_login_block">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
</div>
</body>
</html>
