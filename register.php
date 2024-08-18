<?php
// register.php

// Подключаем файл с конфигурацией Firebase
require_once 'firebase_config.php'; // Обновите путь к вашему конфигурационному файлу

// Получаем данные из формы
$nickname = $_POST['nickname'] ?? '';

if (!empty($nickname)) {
    // Получаем ID пользователя из Telegram
    $user_id = $_SESSION['user_id'] ?? ''; // Предполагаем, что user_id сохранен в сессии

    if ($user_id) {
        // Создаем ссылку в базе данных с уникальным идентификатором пользователя
        $reference = "users/{$user_id}";
        $data = [
            'nickname' => $nickname,
            'totalClicks' => 0 // Инициализируем клики как 0
        ];

        // Обновляем базу данных
        $database->getReference($reference)->set($data);

        // Перенаправляем пользователя на главную страницу или страницу с подтверждением
        header('Location: /');
        exit;
    } else {
        echo "Ошибка: Не удалось получить ID пользователя.";
    }
} else {
    echo "Ошибка: Ник не может быть пустым.";
}
?>
