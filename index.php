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

<!DOCTYPE html>
<html>
<head>
    <title>RPMcoinbot</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(to right, #ff7e5f, #feb47b); 
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        .container {
            display: block;
            text-align: center;
        }

        .button {
            width: 400px; 
            height: 400px; 
            background-image: url('https://i.imgur.com/dtJW1Wu.png'); 
            background-size: cover; 
            background-position: center; 
            border: none;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            margin-bottom: 20px;
            transition: transform 0.1s ease, box-shadow 0.1s ease; 
        }

        .button:active {
            transform: scale(0.9); 
        }

        .counter {
            font-size: 48px; 
            color: #333;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3); 
        }

        @media only screen and (max-width: 768px) {
            .button {
                width: 200px; 
                height: 200px; 
            }

            .counter {
                font-size: 36px; 
            }
        }

        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-container input {
            font-size: 18px;
            padding: 10px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container button {
            padding: 10px 20px;
            font-size: 18px;
            background-color: #ff7e5f;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #feb47b;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!isset($_COOKIE['userId'])): ?>
            <div class="form-container">
                <h1>Введите ваш ник на RPM</h1>
                <form method="post">
                    <input type="text" name="nickname" placeholder="Ваш ник" required />
                    <button type="submit">Сохранить</button>
                </form>
            </div>
        <?php else: ?>
            <div class="button" onclick="incrementCounter()"></div>
            <div class="counter" id="counter">0</div>
        <?php endif; ?>
    </div>

    <script>
        // Функция для увеличения счётчика
        function incrementCounter() {
            const userId = '<?php echo $userId; ?>'; // ID пользователя из куки

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'userId': userId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'Counter updated') {
                    updateLocalCounter();
                }
            })
            .catch(error => {
                console.error('Error updating counter:', error);
            });
        }

        // Функция для обновления локального счётчика
        function updateLocalCounter() {
            const userId = '<?php echo $userId; ?>'; // ID пользователя из куки

            fetch('?userId=' + userId)
            .then(response => response.json())
            .then(data => {
                document.getElementById('counter').innerText = data.totalClicks;
            })
            .catch(error => {
                console.error('Error reading counter:', error);
            });
        }

        // Инициализация счётчика при загрузке страницы
        document.addEventListener('DOMContentLoaded', updateLocalCounter);
    </script>
</body>
</html>
