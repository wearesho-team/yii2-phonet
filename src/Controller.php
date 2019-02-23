<?php

namespace Wearesho\Phonet\Yii;

use Carbon\Carbon;
use yii\base;
use yii\di;
use yii\filters;
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

    /** @var Phonet\Yii\RepositoryInterface */
    public $repository = Phonet\Yii\RepositoryInterface::class;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init(): void
    {
        $this->repository = di\Instance::ensure($this->repository);
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
        } else {
            $bridgeAt = $request->post('bridgeAt');
            $employeeCaller = $request->post('leg');
            $employeeCaller = new Phonet\Yii\Record\Employee([
                'id' => (int)$employeeCaller['id'],
                'internal_number' => $employeeCaller['ext'],
                'display_name' => $employeeCaller['displayName']
            ]);
            $employeeCallTaker = $request->post('leg2');
            $employeeCallTaker = $employeeCallTaker
                ? new Phonet\Yii\Record\Employee([
                    'id' => (int)$employeeCallTaker['id'],
                    'internal_number' => $employeeCallTaker['ext'],
                    'display_name' => $employeeCallTaker['displayName']
                ])
                : null;
            $serverTime = $request->post('serverTime');
            $subjects = \array_map(
                function (array $subject): Phonet\Yii\Record\Subject {
                    return new Phonet\Yii\Record\Subject([
                        'number' => $subject['num'],
                        'uri' => $subject['url'],
                        'internal_id' => ($subject['id'] ?? null) ? (string)$subject['id'] : null,
                        'name' => isset($subject['name']) ? $subject['name'] : null,
                        'company' => isset($subject['companyName']) ? $subject['companyName'] : null,
                        'priority' => isset($subject['priority']) ? $subject['priority'] : null
                    ]);
                },
                $request->post('otherLegs') ?? []
            );

            $event = new Phonet\Yii\Record\CallEvent([
                'event' => new Phonet\Enum\Event($callEvent),
                'uuid' => $request->post('uuid'),
                'parent_uuid' => $request->post('parentUuid'),
                'domain' => $request->post('accountDomain'),
                'dial_at' => Carbon::createFromTimestamp($request->post('dialAt'))->toDateTimeString(),
                'bridge_at' => $bridgeAt ? Carbon::createFromTimestamp($bridgeAt)->toDateTimeString() : null,
                'direction' => new Phonet\Enum\Direction((int)$request->post('lgDirection')),
                'server_time' => $serverTime ? Carbon::createFromTimestamp($serverTime)->toDateTimeString() : null,
                'employeeCaller' => $employeeCaller,
                'trunk_number' => $request->post('trunkNum'),
                'trunk_name' => $request->post('trunkName')
            ]);

            $employeeCaller->save();
            $employeeCallTaker ? $employeeCallTaker->save() : null;
            $event->employeeCallTaker = $employeeCallTaker ?: null;

            $this->repository->put($event);

            return [];
        }
    }
}
