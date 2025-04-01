<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tank_parts";

// Создаем подключение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>