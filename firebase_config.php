<?php
// firebase_config.php

require 'vendor/autoload.php'; // Обновите путь к автозагрузчику Composer

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

$serviceAccount = ServiceAccount::fromJsonFile('path/to/your/serviceAccountKey.json'); // Укажите путь к вашему ключу
$firebase = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://your-database-name.firebaseio.com') // Укажите URL вашей базы данных
    ->create();

$database = $firebase->getDatabase();
?>
