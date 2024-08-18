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

// Функция для генерации уникального ID пользователя
function generateUserId() {
    return uniqid();
}

// Проверка, существует ли куки с ID пользователя
if (!isset($_COOKIE['userId'])) {
    // Если куки нет, показываем форму для ввода ника
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
        header("Location: " . $_SERVER['PHP_SELF']); // Перезагружаем страницу
        exit();
    }
} else {
    $userId = $_COOKIE['userId'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userId'])) {
    $userId = $_POST['userId'];
    $reference = $database->getReference('users/' . $userId . '/totalClicks');
    $reference->runTransaction(function ($currentClicks) {
        return ($currentClicks ?? 0) + 1;
    });
    echo json_encode(['message' => 'Counter updated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    $reference = $database->getReference('users/' . $userId . '/totalClicks');
    $snapshot = $reference->getSnapshot();
    echo json_encode(['totalClicks' => $snapshot->getValue() ?? 0]);
    exit();
}
?>
