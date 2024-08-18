<?php
// Подключение к Firebase
require 'firebase_config.php';

// Функция для проверки существования пользователя в базе данных
function checkUserExists($userId, $database) {
    $reference = "users/{$userId}";
    $snapshot = $database->getReference($reference)->getSnapshot();
    return $snapshot->exists();
}

// Проверка, есть ли кука с ID пользователя
$userId = $_COOKIE['userId'] ?? '';

if ($userId && checkUserExists($userId, $database)) {
    // Если кука существует и пользователь есть в базе данных
    $showForm = false;
} else {
    // Если куки нет или пользователь не найден
    $showForm = true;
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
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            z-index: 1000;
        }

        .form-container input {
            font-size: 18px;
            padding: 10px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 80%;
            box-sizing: border-box;
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
    <?php if ($showForm): ?>
        <div class="form-container">
            <h1>Введите ваш ID</h1>
            <form action="register.php" method="post">
                <input type="text" name="userId" placeholder="Ваш ID" required />
                <button type="submit">Зарегистрироваться</button>
            </form>
        </div>
    <?php else: ?>
        <div class="container">
            <div class="button" onclick="incrementCounter()"></div>
            <div class="counter" id="counter">0</div>
        </div>
    <?php endif; ?>

    <script>
        // Функция для увеличения счётчика
        function incrementCounter() {
            const userId = '<?php echo $_COOKIE['userId']; ?>'; // ID пользователя из куки

            fetch('update_counter.php', {
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
            const userId = '<?php echo $_COOKIE['userId']; ?>'; // ID пользователя из куки

            fetch('update_counter.php?userId=' + userId)
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
