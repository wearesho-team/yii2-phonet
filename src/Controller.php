<?php

namespace Wearesho\Phonet\Yii;

use Carbon\Carbon;
use yii\base;
use yii\di;
use yii\filters;
use yii\queue;
use yii\web;
use Wearesho\Phonet;

/**
 * Class Controller
 * @package Wearesho\Phonet\Yii
 */
class Controller extends base\Controller
{
    /** @var Phonet\Yii\IdentityInterface */
    public $identity = Phonet\Yii\IdentityInterface::class;

    /** @var Phonet\Repository */
    public $repository = Phonet\Repository::class;

    /** @var queue\Queue */
    public $queue = 'queue';

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init(): void
    {
        $this->repository = di\Instance::ensure($this->repository);
        $this->queue = di\Instance::ensure($this->queue);
    }

    public function behaviors(): array
    {
        return [
            'ip' => [
                'class' => filters\AccessControl::class,
                'rules' => [
                    [
                        'class' => filters\AccessRule::class,
                        'allow' => true,
                        'ips' => [
                            // Default hardcode Phonet ips
                            '89.184.65.208',
                            '89.184.82.130',
                            '89.184.67.228',
                            '89.184.82.191',
                            '89.184.65.137',
                            '95.213.132.131',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     * @throws web\HttpException
     */
    public function actionIndex(): array
    {
        $request = \Yii::$app->request;
        \Yii::$app->response->format = web\Response::FORMAT_JSON;

        $callEvent = $request->post('event') ?? false;

        if (!$callEvent) {
            return $this->handleClientRequest($request);
        } else {
            $uuid = $request->post('uuid');
            $event = new Phonet\Call\Event($callEvent);
            $dial = Phonet\Call\Event::DIAL();
            $bridge = Phonet\Call\Event::BRIDGE();
            $hangup = Phonet\Call\Event::HANGUP();

            // Events from Phonet can duplicates.
            // So if `call` with unique uuid and equal type already exist we do not need handle it
            if (!$this->isEventDuplicated($uuid, $dial) && $event->equals($dial)) {
                $this->handleDial($request, $uuid);
            }

            if (!$this->isEventDuplicated($uuid, $bridge) && $event->equals($bridge)) {
                $this->handleBridge($request, $uuid);
            }

            if (!$this->isEventDuplicated($uuid, $hangup) && $event->equals($hangup)) {
                $this->handleHangup($request, $uuid);
            }

            return [];
        }
    }

    /**
     * @param web\Request $request
     *
     * @return array
     * @throws web\HttpException
     */
    protected function handleClientRequest(web\Request $request): array
    {
        $client = $this->identity::findBy(
            $request->post('otherLegNum'),
            $request->post('trunkNum')
        );

        if (\is_null($client)) {
            throw new web\HttpException(400);
        }

        $response = [
            'name' => $client->getName(),
            'url' => $client->getUrl(),
            'urlText' => $client->getUrlText(),
        ];

        if (\is_null($response['name'])) {
            return \array_merge($response, [
                'name' => null,
                'newEntity' => true,
                'responsibleEmployeeExt' => null,
                'responsibleEmployeeEmail' => null
            ]);
        }

        return \array_merge($response, [
            'newEntity' => false,
            'responsibleEmployeeExt' => $client->getResponsibleEmployeeExt(),
            'responsibleEmployeeEmail' => $client->getResponsibleEmployeeEmail()
        ]);
    }

    /**
     * @param web\Request $request
     * @param string $uuid
     *
     * @throws web\HttpException
     */
    protected function handleDial(web\Request $request, string $uuid): void
    {
        $employeeCaller = $request->post('leg');
        $id = (int)$employeeCaller['id'];
        $operator = Phonet\Yii\Record\Employee::find()->andWhere(['id' => $id])->one();

        if (!$operator) {
            $operator = new Phonet\Yii\Record\Employee([
                'id' => $id,
                'internal_number' => $employeeCaller['ext'],
                'display_name' => $employeeCaller['displayName'],
            ]);

            if (!$operator->save()) {
                throw new web\HttpException(400, 'Failed handle call.dial event, operator (leg) validation errors');
            }
        }

        $type = new Phonet\Yii\Call\Type((int)$request->post('lgDirection'));
        $call = new Phonet\Yii\Record\Call([
            'uuid' => $uuid,
            'parent_uuid' => $request->post('parentUuid'),
            'domain' => $request->post('accountDomain'),
            'type' => $type,
            'operator_id' => $operator->id,
            'pause' => Phonet\Yii\Call\Pause::OFF(),
            'dial_at' => Carbon::createFromTimestamp($request->post('dialAt'))->toDateTimeString(),
            'bridge_at' => null,
            'updated_at' => $this->fetchServerTime($request),
            'state' => Phonet\Call\Event::DIAL(),
        ]);

        if (!$call->save()) {
            throw new web\HttpException(400, 'Failed handle call.dial event, call data validation errors');
        }

        if ($type->equals(Phonet\Yii\Call\Type::INTERNAL())) {
            $employeeCallTaker = $request->post('leg2');
            $id = (int)$employeeCallTaker['id'];
            $target = Phonet\Yii\Record\Employee::find()->andWhere(['id' => $id])->one();

            if (!$target) {
                $target = new Phonet\Yii\Record\Employee([
                    'id' => $id,
                    'internal_number' => $employeeCallTaker['ext'],
                    'display_name' => $employeeCallTaker['displayName']
                ]);

                if (!$target->save()) {
                    throw new web\HttpException(400, 'Failed handle call.dial event, target (leg2) validation errors');
                }
            }

            $internalData = new Phonet\Yii\Record\Call\Internal\Data([
                'call_id' => $call->id,
                'operator_id' => $target->id,
            ]);
            $internalData->save();
        } elseif ($type->equals(Phonet\Yii\Call\Type::EXTERNAL_OUT())
            || $type->equals(Phonet\Yii\Call\Type::EXTERNAL_IN())
        ) {
            $subjects = $request->post('otherLegs');
            $subject = array_shift($subjects);
            $externalData = new Phonet\Yii\Record\Call\External\Data([
                'call_id' => $call->id,
                'trunk_name' => $request->post('trunkName'),
                'trunk_number' => $request->post('trunkNum'),
                'subject_number' => $subject['num'],
            ]);

            if (!$externalData->save()) {
                throw new web\HttpException(
                    400,
                    'Failed handle call.dial event, subjects (otherLegs) validation errors'
                );
            }
        }
    }

    /**
     * @param web\Request $request
     * @param $uuid
     *
     * @throws web\HttpException
     */
    public function handleBridge(web\Request $request, string $uuid): void
    {
        $call = $this->getCall($uuid);

        if (!$call) {
            throw new web\HttpException(
                400,
                'Failed handle call.bridge event because of not exist call.dial event in history'
            );
        }

        $call->updated_at = $this->fetchServerTime($request);
        $call->bridge_at = Carbon::createFromTimestamp($request->post('bridgeAt'))->toDateTimeString();
        $call->state = Phonet\Call\Event::BRIDGE();
        $direction = $request->post('lgDirection');

        if (Phonet\Yii\Call\Pause::isValid($direction)) {
            $call->pause = new Phonet\Yii\Call\Pause($direction);
        }

        if (!$call->bridge_at) {
            throw new web\HttpException(
                400,
                'Event call.bridge must contain bridge_at field'
            );
        }

        try {
            $call->update();
        } catch (\Throwable $exception) {
            throw new web\HttpException(500, "Internal error with update data for [$uuid]");
        }
    }

    /**
     * @param web\Request $request
     * @param string $uuid
     *
     * @throws web\HttpException
     */
    public function handleHangup(web\Request $request, string $uuid): void
    {
        $call = $this->getCall($uuid);

        if (!$call) {
            throw new web\HttpException(
                400,
                'Failed handle call.hangup event because of not exist call.dial or call.bridge event in history'
            );
        }

        $call->updated_at = $this->fetchServerTime($request);
        $call->state = Phonet\Call\Event::HANGUP();

        try {
            $call->update();
        } catch (\Throwable $exception) {
            throw new web\HttpException(500, "Internal error with update data for [$uuid]");
        }

        $job = new Phonet\Yii\Job\Call\Complete\Receive(
            $this->repository,
            $uuid,
            Carbon::make($call->dial_at),
            Carbon::make($request->post('serverTime')) ?? Carbon::now()
        );

        $this->queue->push($job);
    }

    protected function fetchServerTime(web\Request $request): ?string
    {
        $serverTime = $request->post('serverTime');

        return ($serverTime ? Carbon::createFromTimestamp($serverTime) : Carbon::now())->toDateTimeString();
    }

    protected function getCall(string $uuid): ?Phonet\Yii\Record\Call
    {
        return Phonet\Yii\Record\Call::find()->andWhere(['uuid' => $uuid])->one();
    }

    protected function isEventDuplicated(string $uuid, Phonet\Call\Event $event): bool
    {
        return Phonet\Yii\Record\Call::find()->andWhere(['uuid' => $uuid, 'state' => $event->getValue()])->count() > 0;
    }
}
