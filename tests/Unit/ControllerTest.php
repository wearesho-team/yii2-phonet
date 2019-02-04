<?php

namespace Wearesho\Phonet\Yii\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wearesho\Phonet\Yii;
use yii\di\Container;
use yii\base\Module;
use yii\web\User;

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
            ->setSingleton(Yii\RepositoryInterface::class, Yii\Tests\Mock\Repository::class)
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

    public function testPutCall(): void
    {
        $controller = new Yii\Controller('id', $this->createMock(Module::class), [
            'identity' => Yii\Tests\Mock\User::class,
        ]);

        $bodyParams = \json_decode(
            '{
              "event": "call.dial",
              "accountDomain": "qwerty.phonet.com.ua",
              "uuid" : "47a968893984475b8c20e29dec144ce3",
              "parentUuid" : null,
              "dialAt" : 1431686100,
              "bridgeAt" : null,
              "lgDirection" : 2,
              "leg": { "id" : 36, "ext": "001", "displayName": "Иван Иванов" },
              "leg2" : null,
              "otherLegs" : [{
                "id": 1,
                "name": "Анастасия Березкина",
                "num": "+380000000000",
                "companyName": "Тестовая компания",
                "url": "http://phonet.com.ua/contacts/1"
              }],
              "trunkNum" : "+380442246595",
              "trunkName" : "www.phonet.com.ua"
            }',
            true
        );

        \Yii::$app->request->setBodyParams($bodyParams);

        $_SERVER['REMOTE_ADDR'] = '00.000.00.000';

        $this->assertEmpty($controller->actionIndex());

        $this->assertEquals(
            [
                new Yii\Model\CallEvent(
                    'call.dial',
                    '47a968893984475b8c20e29dec144ce3',
                    null,
                    'qwerty.phonet.com.ua',
                    1431686100,
                    null,
                    2,
                    null,
                    [
                        'id' => 36,
                        'ext' => '001',
                        'displayName' => 'Иван Иванов',
                    ],
                    null,
                    [
                        [
                            'id' => 1,
                            'name' => 'Анастасия Березкина',
                            'num' => '+380000000000',
                            'companyName' => 'Тестовая компания',
                            'url' => 'http://phonet.com.ua/contacts/1',
                        ]
                    ],
                    '+380442246595',
                    'www.phonet.com.ua'
                )
            ],
            \Yii::$container->get(Yii\RepositoryInterface::class)->getCalls()
        );
    }
}
