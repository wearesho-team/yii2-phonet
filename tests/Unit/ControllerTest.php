<?php

namespace Wearesho\Phonet\Yii\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wearesho\Phonet\Yii;
use Wearesho\Phonet\Yii\Tests\Mock\User;
use yii\di\Container;
use yii\base\Module;

/**
 * Class ControllerTest
 * @package Wearesho\Phonet\Yii\Tests\Unit
 */
class ControllerTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        \Yii::$app = new \yii\web\Application([
            'id' => 'yii2-phonet',
            'basePath' => \Yii::getAlias('@Wearesho/Phonet/Yii'),
            'components' => [
                'user' => [
                    'identityClass' => Yii\Tests\Mock\User::class
                ]
            ]
        ]);

        (\Yii::$container = new Container())
            ->set(Yii\RepositoryInterface::class, Yii\Tests\Mock\Repository::class)
            ->set(User::class, [
                'class' => User::class,
                'identityClass' => Yii\Tests\Mock\User::class,
            ]);
    }

    public function testExistClientName(): void
    {
        $controller = new Yii\Controller('id', $this->createMock(Module::class), [
            'identity' => Yii\Tests\Mock\User::class,
        ]);

        \Yii::$app->request->setBodyParams([
            'otherLegNum' => 'other-leg-num'
        ]);

        $_SERVER['REMOTE_ADDR'] = '00.000.00.000';

        $this->assertEquals(
            [
                'name' => 'name',
                'url' => 'url',
                'urlText' => 'url-text',
                'newEntity' => false,
                'responsibleEmployeeEmail' => 'email',
                'responsibleEmployeeExt' => 'number'
            ],
            $controller->actionIndex()
        );
    }
}
