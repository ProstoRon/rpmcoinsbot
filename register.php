<?php
// Путь к вашему файлу конфигурации Firebase
require_once 'firebase_config.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

// Получение данных из формы
$userId = $_POST['userId'] ?? null;

// Проверка, что ID пользователя передан
if ($userId) {
    $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/path/to/your/firebase_config.json');
    $firebase = (new Factory)->withServiceAccount($serviceAccount)->create();
    $database = $firebase->getDatabase();

    // Ссылка на путь в базе данных, где будем проверять наличие ID
    $reference = $database->getReference('users/' . $userId);
    $snapshot = $reference->getSnapshot();

    // Если ID не существует, создаем нового пользователя
    if (!$snapshot->exists()) {
        $reference->set([
            'userId' => $userId
        ]);
        setcookie('userId', $userId, time() + 3600, "/"); // Установка куки на 1 час
        header('Location: index.html'); // Перенаправление на главную страницу
        exit();
    } else {
        echo "Пользователь с таким ID уже существует!";
    }
} else {
    echo "ID пользователя не передан!";
}
?>
