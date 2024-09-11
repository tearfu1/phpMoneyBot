<?php
require 'vendor/autoload.php';

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;

// Настройки подключения к базе данных
$dbHost = 'localhost';
$dbName = 'telegram_bot';
$dbUser = 'root';
$dbPass = '';

// Подключение к базе данных
$pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Токен вашего бота
$botToken = '6964808672:AAHnFx2pttYCV7grTjNcqvhv32EmtI4D1f0';
$bot = new BotApi($botToken);

// Функция для обработки входящих сообщений
function handleUpdate($update, $pdo, $bot) {
    $message = $update->getMessage();
    $chatId = $message->getChat()->getId();
    $text = $message->getText();

    // Проверка, является ли сообщение числом
    $amount = str_replace(',', '.', $text);
    if (is_numeric($amount)) {
        $amount = (float)$amount;
        $userId = $message->getFrom()->getId();

        // Получение или создание пользователя в базе данных
        $stmt = $pdo->prepare("SELECT * FROM users WHERE telegram_id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $stmt = $pdo->prepare("INSERT INTO users (telegram_id, balance) VALUES (?, ?)");
            $stmt->execute([$userId, 0.00]);
            $user = ['balance' => 0.00];
        }

        // Обновление баланса
        $newBalance = $user['balance'] + $amount;
        if ($newBalance < 0) {
            $bot->sendMessage($chatId, "Ошибка: недостаточно средств на счете.");
            return;
        }

        $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE telegram_id = ?");
        $stmt->execute([$newBalance, $userId]);

        $bot->sendMessage($chatId, "Баланс обновлен. Текущий баланс: $" . number_format($newBalance, 2));
    } else {
        $bot->sendMessage($chatId, "Пожалуйста, отправьте число для обновления баланса.");
    }
}

// Получение обновлений от Telegram
$update = json_decode(file_get_contents('php://input'), true);
$update = Update::fromResponse($update);

handleUpdate($update, $pdo, $bot);