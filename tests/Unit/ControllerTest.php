<?php

namespace Wearesho\Phonet\Yii\Tests\Unit;

use chillerlan\SimpleCache;
use GuzzleHttp;
use Wearesho\Phonet;
use yii\base\Module;
use yii\web\HttpException;
use yii\web\User;

/**
 * Class ControllerTest
 * @package Wearesho\Phonet\Yii\Tests\Unit
 */
class ControllerTest extends Phonet\Yii\Tests\Unit\TestCase
{
    protected const DOMAIN = 'test4.domain.com.ua';
    protected const API_KEY = 'test-api-key';

    /** @var GuzzleHttp\Handler\MockHandler */
    protected $mock;

    /** @var array */
    protected $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = [];
        $history = GuzzleHttp\Middleware::history($this->container);
        $this->mock = new GuzzleHttp\Handler\MockHandler();
        $stack = GuzzleHttp\HandlerStack::create($this->mock);
        $stack->push($history);
        $config = new Phonet\Config(
            static::DOMAIN,
            static::API_KEY
        );
        $client = new GuzzleHttp\Client(['handler' => $stack,]);
        $provider = new Phonet\Authorization\CacheProvider(
            new SimpleCache\Cache(
                new SimpleCache\Drivers\MemoryCacheDriver()
            ),
            $client
        );
        \Yii::$container
            ->set(GuzzleHttp\ClientInterface::class, $client)
            ->set(Phonet\ConfigInterface::class, $config)
            ->set('queue', $this->createMock(\yii\queue\file\Queue::class))
            ->set(Phonet\Authorization\ProviderInterface::class, $provider)
            ->set(User::class, [
                'class' => User::class,
                'identityClass' => Phonet\Yii\Tests\Mock\User::class,
            ]);
    }

    public function testExistClientName(): void
    {
        $controller = new Phonet\Yii\Controller('id', $this->createMock(Module::class), [
            'identity' => Phonet\Yii\Tests\Mock\User::class,
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

    public function testHandleDial(): void
    {
        $controller = new Phonet\Yii\Controller('id', $this->createMock(Module::class), [
            'identity' => Phonet\Yii\Tests\Mock\User::class,
        ]);

        $bodyParams = [
            "event" => "call.dial",
            "accountDomain" => "qwerty.phonet.com.ua",
            "uuid" => "47a968893984475b8c20e29dec144ce3",
            "parentUuid" => null,
            "dialAt" => 1431686100,
            "bridgeAt" => null,
            "lgDirection" => 2,
            "leg" => ["id" => 36, "ext" => "001", "displayName" => "Иван Иванов"],
            "leg2" => null,
            "otherLegs" => [
                [
                    "id" => 1,
                    "name" => "Анастасия Березкина",
                    "num" => "+380000000000",
                    "companyName" => "Тестовая компания",
                    "url" => "http =>//phonet.com.ua/contacts/1"
                ]
            ],
            "trunkNum" => "+380442246595",
            "trunkName" => "www.phonet.com.ua"
        ];

        \Yii::$app->request->setBodyParams($bodyParams);

        $_SERVER['REMOTE_ADDR'] = '00.000.00.000';

        $this->assertEmpty($controller->actionIndex());

        $call = Phonet\Yii\Record\Call::find()->andWhere(['uuid' => '47a968893984475b8c20e29dec144ce3'])->one();

        $this->assertNotNull($call);
        $this->assertEquals(Phonet\Call\Event::DIAL(), $call->state);
    }

    public function testHandleBridge(): void
    {
        $controller = new Phonet\Yii\Controller('id', $this->createMock(Module::class), [
            'identity' => Phonet\Yii\Tests\Mock\User::class,
        ]);

        $bodyParams = [
            "event" => "call.dial",
            "accountDomain" => "qwerty.phonet.com.ua",
            "uuid" => "47a968893984475b8c20e29dec144ce3",
            "parentUuid" => null,
            "dialAt" => 1431686100,
            "bridgeAt" => null,
            "lgDirection" => 2,
            "leg" => ["id" => 36, "ext" => "001", "displayName" => "Иван Иванов"],
            "leg2" => null,
            "otherLegs" => [
                [
                    "id" => 1,
                    "name" => "Анастасия Березкина",
                    "num" => "+380000000000",
                    "companyName" => "Тестовая компания",
                    "url" => "http =>//phonet.com.ua/contacts/1"
                ]
            ],
            "trunkNum" => "+380442246595",
            "trunkName" => "www.phonet.com.ua"
        ];

        \Yii::$app->request->setBodyParams($bodyParams);

        $_SERVER['REMOTE_ADDR'] = '00.000.00.000';

        $this->assertEmpty($controller->actionIndex());

        $bodyParams = [
            "event" => "call.bridge",
            "accountDomain" => "qwerty.phonet.com.ua",
            "uuid" => "47a968893984475b8c20e29dec144ce3",
            "parentUuid" => null,
            "dialAt" => 1431686100,
            "bridgeAt" => 1431686112,
            "lgDirection" => 2,
            "leg" => ["id" => 36, "ext" => "001", "displayName" => "Иван Иванов"],
            "leg2" => null,
            "otherLegs" => [
                [
                    "id" => 1,
                    "name" => "Анастасия Березкина",
                    "num" => "+380000000000",
                    "companyName" => "Тестовая компания",
                    "url" => "http =>//phonet.com.ua/contacts/1"
                ]
            ],
            "trunkNum" => "+380442246595",
            "trunkName" => "www.phonet.com.ua"
        ];

        \Yii::$app->request->setBodyParams($bodyParams);

        $_SERVER['REMOTE_ADDR'] = '00.000.00.000';

        $this->assertEmpty($controller->actionIndex());

        $call = Phonet\Yii\Record\Call::find()->andWhere(['uuid' => '47a968893984475b8c20e29dec144ce3'])->one();

        $this->assertNotNull($call);
        $this->assertEquals(Phonet\Call\Event::BRIDGE(), $call->state);
        $this->assertNotEmpty($call->bridge_at);
    }

    public function testExceptionHandleBridgeWithEmptyDial(): void
    {
        $controller = new Phonet\Yii\Controller('id', $this->createMock(Module::class), [
            'identity' => Phonet\Yii\Tests\Mock\User::class,
        ]);

        $bodyParams = [
            "event" => "call.bridge",
            "accountDomain" => "qwerty.phonet.com.ua",
            "uuid" => "47a968893984475b8c20e29dec144ce3",
            "parentUuid" => null,
            "dialAt" => 1431686100,
            "bridgeAt" => 1431686112,
            "lgDirection" => 2,
            "leg" => ["id" => 36, "ext" => "001", "displayName" => "Иван Иванов"],
            "leg2" => null,
            "otherLegs" => [
                [
                    "id" => 1,
                    "name" => "Анастасия Березкина",
                    "num" => "+380000000000",
                    "companyName" => "Тестовая компания",
                    "url" => "http =>//phonet.com.ua/contacts/1"
                ]
            ],
            "trunkNum" => "+380442246595",
            "trunkName" => "www.phonet.com.ua"
        ];

        \Yii::$app->request->setBodyParams($bodyParams);

        $_SERVER['REMOTE_ADDR'] = '00.000.00.000';

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(
            'Failed handle call.bridge event because of not exist call.dial event in history'
        );

        $controller->actionIndex();
    }

    public function testExceptionHandleHangupWithEmptyDialAndBridge(): void
    {
        $controller = new Phonet\Yii\Controller('id', $this->createMock(Module::class), [
            'identity' => Phonet\Yii\Tests\Mock\User::class,
        ]);

        $bodyParams = [
            "event" => "call.hangup",
            "accountDomain" => "qwerty.phonet.com.ua",
            "uuid" => "47a968893984475b8c20e29dec144ce3",
            "parentUuid" => null,
            "dialAt" => 1431686100,
            "bridgeAt" => 1431686112,
            "lgDirection" => 2,
            "leg" => ["id" => 36, "ext" => "001", "displayName" => "Иван Иванов"],
            "leg2" => null,
            "otherLegs" => [
                [
                    "id" => 1,
                    "name" => "Анастасия Березкина",
                    "num" => "+380000000000",
                    "companyName" => "Тестовая компания",
                    "url" => "http =>//phonet.com.ua/contacts/1"
                ]
            ],
            "trunkNum" => "+380442246595",
            "trunkName" => "www.phonet.com.ua"
        ];

        \Yii::$app->request->setBodyParams($bodyParams);

        $_SERVER['REMOTE_ADDR'] = '00.000.00.000';

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(
            'Failed handle call.hangup event because of not exist call.dial or call.bridge event in history'
        );

        $this->assertEmpty($controller->actionIndex());
    }

    public function testHandleHangup(): void
    {
        $controller = new Phonet\Yii\Controller('id', $this->createMock(Module::class), [
            'identity' => Phonet\Yii\Tests\Mock\User::class,
        ]);

        $bodyParams = [
            "event" => "call.dial",
            "accountDomain" => "qwerty.phonet.com.ua",
            "uuid" => "47a968893984475b8c20e29dec144ce3",
            "parentUuid" => null,
            "dialAt" => 1431686100,
            "bridgeAt" => null,
            "lgDirection" => 2,
            "leg" => ["id" => 36, "ext" => "001", "displayName" => "Иван Иванов"],
            "leg2" => null,
            "otherLegs" => [
                [
                    "id" => 1,
                    "name" => "Анастасия Березкина",
                    "num" => "+380000000000",
                    "companyName" => "Тестовая компания",
                    "url" => "http =>//phonet.com.ua/contacts/1"
                ]
            ],
            "trunkNum" => "+380442246595",
            "trunkName" => "www.phonet.com.ua"
        ];

        \Yii::$app->request->setBodyParams($bodyParams);

        $this->assertEmpty($controller->actionIndex());

        $bodyParams = [
            "event" => "call.bridge",
            "accountDomain" => "qwerty.phonet.com.ua",
            "uuid" => "47a968893984475b8c20e29dec144ce3",
            "parentUuid" => null,
            "dialAt" => 1431686100,
            "bridgeAt" => 1431686112,
            "lgDirection" => 2,
            "leg" => ["id" => 36, "ext" => "001", "displayName" => "Иван Иванов"],
            "leg2" => null,
            "otherLegs" => [
                [
                    "id" => 1,
                    "name" => "Анастасия Березкина",
                    "num" => "+380000000000",
                    "companyName" => "Тестовая компания",
                    "url" => "http =>//phonet.com.ua/contacts/1"
                ]
            ],
            "trunkNum" => "+380442246595",
            "trunkName" => "www.phonet.com.ua"
        ];

        \Yii::$app->request->setBodyParams($bodyParams);

        $this->assertEmpty($controller->actionIndex());

        $bodyParams = [
            "event" => "call.hangup",
            "accountDomain" => "qwerty.phonet.com.ua",
            "uuid" => "47a968893984475b8c20e29dec144ce3",
            "parentUuid" => null,
            "dialAt" => 1431686100,
            "bridgeAt" => 1431686112,
            "lgDirection" => 2,
            "leg" => ["id" => 36, "ext" => "001", "displayName" => "Иван Иванов"],
            "leg2" => null,
            "otherLegs" => [
                [
                    "id" => 1,
                    "name" => "Анастасия Березкина",
                    "num" => "+380000000000",
                    "companyName" => "Тестовая компания",
                    "url" => "http =>//phonet.com.ua/contacts/1"
                ]
            ],
            "trunkNum" => "+380442246595",
            "trunkName" => "www.phonet.com.ua"
        ];

        \Yii::$app->request->setBodyParams($bodyParams);

        $_SERVER['REMOTE_ADDR'] = '00.000.00.000';

        $this->assertEmpty($controller->actionIndex());

        $call = Phonet\Yii\Record\Call::find()->andWhere(['uuid' => '47a968893984475b8c20e29dec144ce3'])->one();

        $this->assertNotNull($call);
        $this->assertEquals(Phonet\Call\Event::HANGUP(), $call->state);
    }

    public function testHandleInternalCall(): void
    {

        $controller = new Phonet\Yii\Controller('id', $this->createMock(Module::class), [
            'identity' => Phonet\Yii\Tests\Mock\User::class,
        ]);

        $bodyParams = [
            "event" => "call.dial",
            "accountDomain" => "qwerty.phonet.com.ua",
            "uuid" => "47a968893984475b8c20e29dec144ce3",
            "parentUuid" => null,
            "dialAt" => 1431686100,
            "bridgeAt" => null,
            "lgDirection" => 1,
            "leg" => ["id" => 36, "ext" => "001", "displayName" => "Иван Иванов"],
            "leg2" => ["id" => 34, "ext" => "001", "displayName" => "Иван Солдатов"],
            "otherLegs" => null,
            "trunkNum" => "+380442246595",
            "trunkName" => "www.phonet.com.ua"
        ];

        \Yii::$app->request->setBodyParams($bodyParams);

        $this->assertEmpty($controller->actionIndex());

        $call = Phonet\Yii\Record\Call::find()->andWhere(['uuid' => '47a968893984475b8c20e29dec144ce3'])->one();

        $this->assertNotNull($call);
        $this->assertEquals(Phonet\Call\Event::DIAL(), $call->state);
        $this->assertNotEmpty($call->internalData);
    }
}
