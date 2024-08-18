<?php
// Подключение к Firebase
require 'firebase_config.php';

// Получение ID пользователя из формы
$userId = $_POST['userId'] ?? '';

if (!empty($userId)) {
    // Проверка существования записи с таким ID
    if (checkUserExists($userId, $database)) {
        // Создание куки с ID пользователя
        setcookie('userId', $userId, time() + (86400 * 30), "/"); // Кука на 30 дней

        // Перенаправление на главную страницу
        header('Location: index.php');
        exit();
    } else {
        echo "ID не найден в базе данных.";
    }
} else {
    echo "ID не был предоставлен.";
}
?>
