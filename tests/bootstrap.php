<?php

if (\file_exists(\dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env')) {
    $dotEnv = \Dotenv\Dotenv::create(\dirname(__DIR__));
    $dotEnv->load();
}

\Yii::setAlias(
    '@Wearesho/Phonet/Yii',
    \dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src'
);
\Yii::setAlias('@configFile', __DIR__ . DIRECTORY_SEPARATOR . 'config.php');
