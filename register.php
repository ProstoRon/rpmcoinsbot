<?php
// Подключение к Firebase
require 'vendor/autoload.php'; // Подключаем autoload.php для Firebase PHP SDK

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

// Замените путь на путь к вашему JSON-файлу конфигурации
$serviceAccount = ServiceAccount::fromJsonFile('path/to/your/firebase-adminsdk.json');
$firebase = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://rpmcoins-48e4f-default-rtdb.firebaseio.com')
    ->create();

$database = $firebase->getDatabase();

// Получаем данные из формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $nickname = $_POST['nickname'];

    // Проверка наличия записи в базе данных
    $userRef = $database->getReference("users/$user_id");
    $snapshot = $userRef->getSnapshot();
    
    if ($snapshot->exists()) {
        // Запись существует, создаем аккаунт и перенаправляем
        $userRef->getChild($nickname)->set(['totalClicks' => 0]);
        header("Location: https://prostoron.github.io/rpmcoinsbot/clicker.html"); // Замените на URL вашего клика
    } else {
        // Запись не найдена
        echo "ID не найдено. Пожалуйста, проверьте ваш ID.";
    }
} else {
    echo "Неверный метод запроса.";
}
?>
