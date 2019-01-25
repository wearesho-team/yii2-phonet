<?php

namespace Wearesho\Phonet\Yii;

use Horat1us\Yii\Traits\BootstrapMigrations;
use yii\base;
use yii\console;

/**
 * Class Bootstrap
 * @package Wearesho\Phonet\Yii
 */
class Bootstrap implements base\BootstrapInterface
{
    use BootstrapMigrations;

    public function bootstrap($app): void
    {
        if ($app instanceof console\Application) {
            $this->appendMigrations($app, 'Wearesho\Phonet\Yii\Migration');
        }
    }
}
