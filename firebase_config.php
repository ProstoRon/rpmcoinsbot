<?php
require 'vendor/autoload.php'; // Подключение автозагрузчика Composer

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

$serviceAccount = ServiceAccount::fromJsonFile('serviceAccountKey.json');

$firebase = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://rpmcoins-48e4f-default-rtdb.firebaseio.com')
    ->create();

$database = $firebase->getDatabase();
?>
