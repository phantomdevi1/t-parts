<?php
require 'config.php';
session_start();
// Если пользователь уже авторизован, перенаправляем
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $isAdmin = 0;

        if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm)) {
            $error_message = "Пожалуйста, заполните все поля.";
        } elseif (!preg_match('/^(\+7|8)\d{10}$/', $phone)) {
            $error_message = "Введите корректный российский номер телефона.";
        } elseif ($password !== $confirm) {
            $error_message = "Пароли не совпадают.";
        }else {
        $check_sql = "SELECT id FROM users WHERE email = ? OR phone = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Ой-ой. Такой пользователь уже есть.";
        } else {
            $insert_sql = "INSERT INTO users (username, password, email, phone, is_admin) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sssss", $name, $password, $email, $phone, $isAdmin);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                header("Location: account.php");
                exit();
            } else {
                $error_message = "Ошибка при регистрации. Попробуйте позже.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Регистрация</title>
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
            <a href="<?php echo $is_logged_in ? 'account.php' : 'login.php'; ?>" class="icon-link"><img src="img/profile_icon.png" alt=""></a>
            </div>
        </div>
    </header>

<div class="content">
    <div class="login_container">
        <h1>Регистрация</h1>
        <?php if (isset($error_message)) echo "<p style='color: #fff;'>$error_message</p>"; ?>

        <form method="post" class="registr-form">
            <input type="text" name="name" required placeholder="Имя">
            <input type="email" name="email" required placeholder="E-mail">
            <input type="text" name="phone" required placeholder="Номер телефона" pattern="^(\+7|8)\d{10}$" title="Введите номер в формате +7XXXXXXXXXX или 8XXXXXXXXXX">

            <input type="password" name="password" required placeholder="Пароль">
            <input type="password" name="confirm_password" required placeholder="Подтверждение пароля">
            <button class="registration_btn" type="submit">Зарегистрироваться</button>
        </form>

        <p class="registr_login_block">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</div>
</body>
</html>
