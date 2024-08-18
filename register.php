<?php
session_start();

// Подключение к Firebase
require 'vendor/autoload.php'; // Подключение автозагрузчика Composer

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

$serviceAccount = ServiceAccount::fromJsonFile('path/to/your/serviceAccountKey.json');
$firebase = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://rpmcoins-48e4f-default-rtdb.firebaseio.com')
    ->create();

$database = $firebase->getDatabase();

// Генерация уникального ID пользователя
function generateUserId() {
    return uniqid();
}

// Обработка формы регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nickname'])) {
    $nickname = htmlspecialchars($_POST['nickname']);
    $userId = generateUserId();

    // Сохраняем никнейм и ID пользователя в Firebase
    $database->getReference('users/' . $userId)->set([
        'nickname' => $nickname,
        'totalClicks' => 0
    ]);

    // Устанавливаем куки с ID пользователя
    setcookie('userId', $userId, time() + (86400 * 30), "/"); // Куки истекает через 30 дней
    header("Location: index.html"); // Перенаправляем на основную страницу
    exit();
}
?>
