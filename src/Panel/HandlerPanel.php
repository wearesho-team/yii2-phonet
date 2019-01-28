<?php

namespace Wearesho\Phonet\Yii\Panel;

use Horat1us\Yii\Exceptions\ModelException;
use Horat1us\Yii\Interfaces\ModelExceptionInterface;
use Horat1us\Yii\Validators\InstanceValidator;
use Wearesho\Phonet;
use Wearesho\Yii;
use yii\filters;
use yii\web\HttpException;
use yii\di;

/**
 * Class HandlerPanel
 * @package Wearesho\Phonet\Yii\Panel
 */
class HandlerPanel extends Yii\Http\Panel
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
                'identity',
                InstanceValidator::class,
                'className' => Phonet\Yii\IdentityInterface::class,
            ],
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
        $callEvent = $this->fetch('event') ?? false;

        if (!$callEvent) {
            $client = $this->identity::findBy(
                $this->fetch('otherLegNum'),
                $this->fetch('request'),
                $this->fetch('trunkNum')
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
                'subjects' => $this->fetch('otherLegs'),
                'direction' => $this->fetch('lgDirection'),
                'uuid' => $this->fetch('uuid'),
                'domain' => $this->fetch('accountDomain'),
                'event' => $callEvent,
                'bridge_at' => $this->fetch('bridgeAt'),
                'dial_at' => $this->fetch('dialAt'),
                'employee_call_taker' => $this->fetch('leg2'),
                'employee_caller' => $this->fetch('leg'),
                'parent_uuid' => $this->fetch('parentUuid'),
                'server_time' => $this->fetch('serverTime'),
                'trunk_name' => $this->fetch('trunkName'),
                'trunk_number' => $this->fetch('trunkNum'),
            ]);

            if (!$event->validate()) {
                throw new HttpException(400, 'Call event did not pass validation');
            };

            $this->repository->put($event);

            return [];
        }
    }

    /**
     * @param string $param
     *
     * @return mixed
     */
    protected function fetch(string $param)
    {
        return isset($_POST[$param]) || \array_key_exists($param, $_POST) ? $_POST[$param] : null;
    }
}
