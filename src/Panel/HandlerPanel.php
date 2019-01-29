<?php

namespace Wearesho\Phonet\Yii\Panel;

use Horat1us\Yii\Validators\InstanceValidator;
use Wearesho\Phonet;
use Wearesho\Yii\Http;
use yii\web\HttpException;
use yii\di;

/**
 * Class HandlerPanel
 * @package Wearesho\Phonet\Yii\Panel
 */
class HandlerPanel extends Http\Panel
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
                'class' => Http\Behaviors\AccessControl::class,
                'rules' => [
                    [
                        'class' => Http\AccessRule::class,
                        'allow' => true,
                        'ips' => [
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

    public function rules(): array
    {
        return [
            [['identity', 'repository',], 'required',],
            [
                'repository',
                InstanceValidator::class,
                'className' => Phonet\Yii\RepositoryInterface::class
            ],
        ];
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function generateResponse(): array
    {
        $callEvent = $this->request->post('event') ?? false;

        if (!$callEvent) {
            $client = $this->identity::findBy(
                $this->request->post('otherLegNum'),
                $this->request->post('trunkNum')
            );

            if (\is_null($client)) {
                throw new HttpException(400);
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
            $event = new Phonet\Yii\Model\CallEvent([
                'subjects' => $this->request->post('otherLegs'),
                'direction' => $this->request->post('lgDirection'),
                'uuid' => $this->request->post('uuid'),
                'domain' => $this->request->post('accountDomain'),
                'event' => $callEvent,
                'bridge_at' => $this->request->post('bridgeAt'),
                'dial_at' => $this->request->post('dialAt'),
                'employee_call_taker' => $this->request->post('leg2'),
                'employee_caller' => $this->request->post('leg'),
                'parent_uuid' => $this->request->post('parentUuid'),
                'server_time' => $this->request->post('serverTime'),
                'trunk_name' => $this->request->post('trunkName'),
                'trunk_number' => $this->request->post('trunkNum'),
            ]);

            if (!$event->validate()) {
                throw new HttpException(400, 'Call event did not pass validation');
            };

            $this->repository->put($event);

            return [];
        }
    }
}
