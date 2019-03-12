<?php

namespace Wearesho\Phonet\Yii\Tests\Unit;

use yii\base\Model;
use yii\phpunit;
use yii\helpers;

/**
 * Class TestCase
 * @package Wearesho\Phonet\Yii\Tests\Unit
 */
class TestCase extends phpunit\TestCase
{
    public function globalFixtures(): array
    {
        $fixtures = [
            [
                'class' => phpunit\MigrateFixture::class,
                'migrationNamespaces' => [
                    'Wearesho\\Phonet\\Yii\\Migrations'
                ]
            ]
        ];

        return helpers\ArrayHelper::merge(parent::globalFixtures(), $fixtures);
    }

    protected function formModelErrors(Model $model): string
    {
        return "Validation errors: " . PHP_EOL
            . \implode(
                "\n",
                \array_map(function (array $error): string {
                    return \implode('; ', $error);
                }, $model->getErrors())
            );
    }
}
