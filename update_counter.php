<?php
// Подключение к Firebase
require 'firebase_config.php';

// Получение ID пользователя из запроса
$userId = $_POST['userId'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_GET['userId'] ?? '';
}

if (!empty($userId)) {
    $reference = "users/{$userId}/totalClicks";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Увеличение счётчика
        $database->getReference($reference)->runTransaction(function($currentValue) {
            return ($currentValue ?? 0) + 1;
        });
        
        $response = ['message' => 'Counter updated'];
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Чтение счётчика
        $snapshot = $database->getReference($reference)->getSnapshot();
        $totalClicks = $snapshot->getValue();
        $response = ['totalClicks' => $totalClicks];
    } else {
        $response = ['error' => 'Invalid request method'];
    }
} else {
    $response = ['error' => 'No userId provided'];
}

header('Content-Type: application/json');
echo json_encode($response);
?>
