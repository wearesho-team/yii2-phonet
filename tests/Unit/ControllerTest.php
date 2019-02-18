<?php

namespace Wearesho\Phonet\Yii\Tests\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Wearesho\Phonet;
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
                    'identityClass' => Phonet\Yii\Tests\Mock\User::class
                ]
            ]
        ]);

        (\Yii::$container = new Container())
            ->setSingleton(Phonet\Yii\RepositoryInterface::class, Phonet\Yii\Tests\Mock\Repository::class)
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

    public function testPutCall(): void
    {
        $controller = new Phonet\Yii\Controller('id', $this->createMock(Module::class), [
            'identity' => Phonet\Yii\Tests\Mock\User::class,
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

        /** @var Phonet\Yii\Data\CallEvent $call */
        $call = \Yii::$container->get(Phonet\Yii\RepositoryInterface::class)->getCalls()[0];
        $this->assertEquals(
            new Phonet\Yii\Data\CallEvent(
                Phonet\Enum\Event::DIAL(),
                '47a968893984475b8c20e29dec144ce3',
                null,
                'qwerty.phonet.com.ua',
                Carbon::createFromTimestamp(1431686100),
                null,
                Phonet\Enum\Direction::OUT(),
                null,
                new Phonet\Yii\Data\Employee(
                    36,
                    '001',
                    'Иван Иванов'
                ),
                null,
                new Phonet\Data\Collection\Subject([
                    new Phonet\Yii\Data\Subject(
                        '+380000000000',
                        'http://phonet.com.ua/contacts/1',
                        1,
                        'Анастасия Березкина',
                        'Тестовая компания'
                    )
                ]),
                '+380442246595',
                'www.phonet.com.ua'
            ),
            $call
        );
        $this->assertEquals('call.dial', $call->event);
        $this->assertEquals('47a968893984475b8c20e29dec144ce3', $call->uuid);
        $this->assertNull($call->getParentUuid());
        $this->assertEquals('qwerty.phonet.com.ua', $call->domain);
        $this->assertEquals(Carbon::createFromTimestamp(1431686100), $call->dialAt);
        $this->assertNull($call->bridgeAt);
        $this->assertEquals(Phonet\Enum\Direction::OUT(), $call->direction);
        $this->assertNull($call->serverTime);
        $this->assertEquals(36, $call->employeeCaller->id);
        $this->assertEquals('001', $call->employeeCaller->internalNumber);
        $this->assertEquals('Иван Иванов', $call->employeeCaller->displayName);
        $this->assertNull($call->employeeCallTaker);
        $this->assertCount(1, $call->subjects);
        /** @var Phonet\Yii\Data\Subject $subject */
        $subject = $call->subjects[0];
        $this->assertEquals('+380000000000', $subject->number);
        $this->assertEquals('http://phonet.com.ua/contacts/1', $subject->uri);
        $this->assertEquals(1, $subject->id);
        $this->assertEquals('Анастасия Березкина', $subject->name);
        $this->assertEquals('Тестовая компания', $subject->company);
    }
}
