<?php

namespace Wearesho\Phonet\Yii;

use Horat1us\Yii\Traits\BootstrapMigrations;
use Wearesho\Phonet;
use yii\base\BootstrapInterface;
use yii\console;

/**
 * Class Bootstrap
 * @package Wearesho\Phonet\Yii
 */
class Bootstrap implements BootstrapInterface
{
    use BootstrapMigrations;

    /** @var string */
    protected $repository = [
        'class' => Phonet\Repository::class,
    ];

    public function bootstrap($app)
    {
        \Yii::setAlias('Wearesho/Phonet/Yii', '@vendor/wearesho-team/yii2-phonet/src');

        if ($app instanceof console\Application) {
            $this->appendMigrations($app, 'Wearesho\\Phonet\\Yii\\Migrations');
        }

        \Yii::$container->setDefinitions([
            Phonet\Authorization\ProviderInterface::class => Phonet\Authorization\Provider::class,
            Phonet\ConfigInterface::class => Phonet\EnvironmentConfig::class,
            Phonet\Repository::class => $this->repository,
        ]);
    }
}
