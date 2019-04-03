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

    /** @var string|array|Phonet\Authorization\ProviderInterface */
    public $provider = [
        'class' => Phonet\Authorization\Provider::class
    ];

    /** @var string|array|Phonet\ConfigInterface */
    public $config = [
        'class' => Phonet\EnvironmentConfig::class
    ];

    /** @var string|array|Phonet\Repository */
    public $repository = [
        'class' => Phonet\Repository::class,
    ];

    public function bootstrap($app)
    {
        \Yii::setAlias('Wearesho/Phonet/Yii', '@vendor/wearesho-team/yii2-phonet/src');

        if ($app instanceof console\Application) {
            $this->appendMigrations($app, 'Wearesho\\Phonet\\Yii\\Migrations');
        }

        $this->configureContainer();
    }

    protected function configureContainer(): void
    {
        \Yii::$container->setDefinitions([
            Phonet\Authorization\ProviderInterface::class => $this->provider,
            Phonet\ConfigInterface::class => $this->config,
            Phonet\Repository::class => $this->repository,
        ]);
    }
}
