<?php
require 'config.php';
session_start();

$q = trim($_GET['q'] ?? '');

$search_results = [];

if ($q !== '') {
    $like = '%' . $q . '%';
    $stmt = $conn->prepare("SELECT id, name, price, image_path FROM parts WHERE name LIKE ?");
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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

            <form method="get" action="search.php" class="search-form">
                <input type="search" name="q" class="search-input" placeholder="Название запчасти" value="<?= htmlspecialchars($q) ?>">
                <button type="submit" class="search-btn">
                    Найти <img src="img/search.png" alt="">
                </button>
            </form>

            <a href="index.php#carsindex" class="icon-link"><img src="img/car.png" alt=""></a>
            <a href="cart.php" class="icon-link"><img src="img/cart.png" alt=""></a>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'account.php' : 'login.php'; ?>" class="icon-link"><img src="img/profile_icon.png" alt=""></a>
        </div>
    </div>
</header>

<div class="container_cart">
    <h1>Результаты поиска по запросу: "<?= htmlspecialchars($q) ?>"</h1>

    <?php if (empty($search_results)): ?>
        <p>Ничего не найдено.</p>
    <?php else: ?>
        <table class="parts_table">
            <tbody>
                <?php foreach ($search_results as $item): ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($item['image_path']) ?>" alt="" width="150"></td>
                        <td class="description_title_td">
                          <?= htmlspecialchars($item['name']) ?>
                          <div class="description_td">
                            <p><?= htmlspecialchars($item['price']) ?> ₽</p>
                          </div>
                        </td>
                        <td>
                            <form action="add_to_cart.php" method="post">
                                <input type="hidden" name="part_id" value="<?= $item['id'] ?>">
                                <input type="number" name="quantity" value="1" min="1" style="width: 60px;">
                                <button type="submit" class="add_cart_btn">В корзину</button>
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
