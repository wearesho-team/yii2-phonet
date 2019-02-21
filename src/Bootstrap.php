<?php

namespace Wearesho\Phonet\Yii;

use Horat1us\Yii\Traits\BootstrapMigrations;
use yii\base\BootstrapInterface;
use yii\console;

/**
 * Class Bootstrap
 * @package Wearesho\Phonet\Yii
 */
class Bootstrap implements BootstrapInterface
{
    use BootstrapMigrations;

    public function bootstrap($app)
    {
        \Yii::setAlias('@Wearesho\Phonet\Yii', '@vendor/wearesho-team/yii2-phonet/src');

        if ($app instanceof console\Application) {
            $this->appendMigrations($app, 'Wearesho/Phonet/Yii/Migrations');
        }
    }
}
