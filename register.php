<?php
// Подключение к Firebase
require 'vendor/autoload.php'; // Подключаем autoload.php для Firebase PHP SDK

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

// Путь к временно созданному файлу
$tempFile = 'keyss.json';

// Создание временного файла конфигурации
$json_key = '{
  "type": "service_account",
  "project_id": "rpmcoins-48e4f",
  "private_key_id": "cc50a0bd4301b12821b61fc64ca010545ee4922b",
  "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCYMef0rbrjj9Ry\n6dTDRNl+aBq4JHl2hlWW2fJWo8LbftF5ea8E0yajX5TXlGjEVejKKLPKnARkpOpa\n5awBjp1ZNaTMC6wkdz9IdBkpe9oC5jNk4EqjDCsffqTwpBaqApIFsZeY8wSQ6vxN\nG/tmdDXel7hJADcYKtvtHKpWoTm66jQNFRdX3Urpz+GCQ2EbAf1hVuT3QmcLAtP1\njCIwR3Tr6hN7bXxAll87wBePl7/e9Tz+11mGE8EdnjhrWh3/eMuWIxcnlNl+PEN2\nikf/ZwlYlgTRh6RbykQWGG75iIlKB/XvgFqpjk3ySG04Yvd05WFqdAA5oqjQb7bz\nRNYezS0nAgMBAAECggEACc3j4+q8fdlaSxbOJSV5H1VZ10qeOn5CjZJiXqYeW4vK\ntxkZfGWVI8l4qf8+VTDgQK7+OgtH6ysxNQQo9aa4Hd2V+AEOfgDikOp4Y9Log0RM\nw5mr+ons+JKXfIW4N2EtV/Q4pa/jYj8zsUygAC8fTe9sfeNnfoU69VuMi5fHmMdR\nKQ25epUYp/jxPOaZiRLJ544OjSKWILej06WkB110JUa+go0XUEbrxoxdH7QxiuMP\nfKlH9D6bi68Vy7yKtdfR36wYko/LxxjEohrtgHiuaES2wikgMezvoX0DwP/7q8sO\nU78pBraSI3oEhRTtDmN9kqMBsg2L5hIf6sgFuKohlQKBgQDICaecjewR9ZLrTVRQ\nVqWIEDiYVVoLPGvhPeBlXQIn8KWCQ2usc+nOJWZImHvFokJ4CDF22WKQxGQ6NyAv\nSeeW+d8WSFITV2JBm52daaOHhYoD0XqpN9KSKJwy1e+9+sxr6vmsZZNGMe6/B6in\n+VGUKhGKYDZlMt9w9/WGShPnJQKBgQDCxdZer2OE6HVcnLv7iJVl3SbxnVI5q4kE\n5CZySq/COs/qEIve2wp9kvjRqNZ47GiRN2b/akASpiMIbqPMTfyr9w0plyvVJVAL\nM3N3ZKWHhwAjOjVx4ppUocLIG+REWED1d/W2BlEahpqp2Ug7xtsFv103HdZXd5AX\nmZYqjEgHWwKBgQCxe/9/ZFzkwmMM65u3fGl6hAny/yfZeE6vltsWlKNpFJeFMJTE\nekQkaAKMvt5yyDj5jyt0LATMNbRIAslHq+cTibhqaNiJ2KqRU6VRGX3CrLV8Fc0j\nfygSDn4ojLEeevwNdHr6f1VgnsUaMY1vHENJltnhUORyfeTKsvbho7BtEQKBgCx3\nYjOHsx66u2CDX99TFViGHsf2dLk50jRNqgFZz7p2FtWHJYehVknI7glflGdTAaR1\nk4woQ6x+RW09ZSPVgiP4aQbfvmAQ/q8v9eKCFdBVH9DrDxyK8VT1ATUSORDpXRst\nYelGk+mMZW71bipHgeVUBQH6S974KutYYxIfqKoVAoGAaHu0jRETx8P5OvO8/L/s\npelGiKv6VfK5RZ8pesdl+QvKWTEJr7o5ocQXnX3zvIhXtzeddMgJdhADSCqsivQA\nGjEXRnYbKx26AKpkoK3zoA4dGlBIWRT+6uFTmKKsffB4q7uHykXO5vzVcHVaOMw1\nao5lT01xqsnQx64Wcj5136Q=\n-----END PRIVATE KEY-----\n",
  "client_email": "firebase-adminsdk-8rmo2@rpmcoins-48e4f.iam.gserviceaccount.com",
  "client_id": "114278276030303711399",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-8rmo2%40rpmcoins-48e4f.iam.gserviceaccount.com",
  "universe_domain": "googleapis.com"
}';

// Создание временного файла
file_put_contents($tempFile, $json_key);

// Подключение к Firebase
$serviceAccount = ServiceAccount::fromJsonFile($tempFile);
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
        header("Location: https://prostoron.github.io/rpmcoinsbot"); // Замените на URL вашего клика
    } else {
        // Запись не найдена
        echo "ID не найдено. Пожалуйста, проверьте ваш ID.";
    }
} else {
    echo "Неверный метод запроса.";
}

// Удаление временного файла после использования
unlink($tempFile);
?>
