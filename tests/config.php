<?php

use yii\helpers\ArrayHelper;
use yii\db\Connection;

$localConfig = __DIR__ . DIRECTORY_SEPARATOR . 'config-local.php';
$dbType = getenv('DB_TYPE');
$host = getenv('DB_HOST');
$name = getenv("DB_NAME");
$port = getenv("DB_PORT");
$dsn = "{$dbType}:host={$host};dbname={$name};port={$port}";
$config = [
    'class' => \yii\web\Application::class,
    'id' => 'yii2-phonet',
    'basePath' => \dirname(__DIR__),
    'components' => [
        'db' => [
            'class' => Connection::class,
            'dsn' => $dsn,
            'username' => \getenv("DB_USERNAME"),
            'password' => \getenv("DB_PASSWORD") ?: null,
        ],
        'user' => [
            'class' => \Wearesho\Phonet\Yii\Tests\Mock\User::class,
            'identityClass' => \Wearesho\Phonet\Yii\Tests\Mock\User::class
        ],
    ],
];

return ArrayHelper::merge(
    $config,
    \is_file($localConfig) ? require $localConfig : []
);
