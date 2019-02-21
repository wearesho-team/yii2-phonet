<?php

namespace Wearesho\Phonet\Yii\Tests\Unit;

use Carbon\Carbon;
use Wearesho\Phonet;
use yii\base\Module;
use yii\web\User;

/**
 * Class ControllerTest
 * @package Wearesho\Phonet\Yii\Tests\Unit
 */
class ControllerTest extends Phonet\Yii\Tests\Unit\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        \Yii::$container
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

        /** @var Phonet\Yii\Record\CallEvent $call */
        $call = \Yii::$container->get(Phonet\Yii\RepositoryInterface::class)->getCalls()[0];
        $callEvent = new Phonet\Yii\Record\CallEvent([
            'id' => 1,
            'event' => Phonet\Enum\Event::DIAL(),
            'uuid' => '47a968893984475b8c20e29dec144ce3',
            'parent_uuid' => null,
            'domain' => 'qwerty.phonet.com.ua',
            'dial_at' => Carbon::createFromTimestamp(1431686100)->toDateTimeString(),
            'bridge_at' => null,
            'direction' => Phonet\Enum\Direction::OUT(),
            'server_time' => null,
            'employeeCaller' => new Phonet\Yii\Record\Employee([
                'id' => 36,
                'internal_number' => '001',
                'display_name' => 'Иван Иванов'
            ]),
            'subjects' => [
                new Phonet\Yii\Record\Subject([
                    'number' => '+380000000000',
                    'uri' => 'http://phonet.com.ua/contacts/1',
                    'internal_id' => 1,
                    'name' => 'Анастасия Березкина',
                    'company' => 'Тестовая компания'
                ])
            ],
            'employeeCallTaker' => null,
            'trunk_number' => '+380442246595',
            'trunk_name' => 'www.phonet.com.ua'
        ]);

        $this->assertEquals($callEvent->attributes, $call->attributes);
        $this->assertEquals('call.dial', $call->event);
        $this->assertEquals('47a968893984475b8c20e29dec144ce3', $call->uuid);
        $this->assertNull($call->parent_uuid);
        $this->assertEquals('qwerty.phonet.com.ua', $call->domain);
        $this->assertEquals(Carbon::createFromTimestamp(1431686100), $call->dial_at);
        $this->assertNull($call->bridge_at);
        $this->assertEquals(Phonet\Enum\Direction::OUT(), $call->direction);
        $this->assertNull($call->server_time);
        $this->assertEquals(36, $call->employeeCaller->id);
        $this->assertEquals('001', $call->employeeCaller->internal_number);
        $this->assertEquals('Иван Иванов', $call->employeeCaller->display_name);
        $this->assertNull($call->employeeCallTaker);
        $this->assertCount(1, $call->subjects);
        /** @var Phonet\Yii\Record\Subject $subject */
        $subject = $call->subjects[0];
        $this->assertEquals('+380000000000', $subject->number);
        $this->assertEquals('http://phonet.com.ua/contacts/1', $subject->uri);
        $this->assertEquals(1, $subject->internal_id);
        $this->assertEquals('Анастасия Березкина', $subject->name);
        $this->assertEquals('Тестовая компания', $subject->company);
    }
}
