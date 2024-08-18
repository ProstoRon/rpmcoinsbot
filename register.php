<?php
// Проверка наличия данных
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $nickname = $_POST['nickname'];

    // Логика обработки данных
    // Например, добавление данных в базу данных
    // ...

    // Ответ клиенту
    echo "Registration successful!";
} else {
    echo "Invalid request method.";
}
?>
