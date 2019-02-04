<?php

namespace Wearesho\Phonet\Yii;

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
            $event = new Phonet\Yii\CallEvent(
                $callEvent,
                $request->post('uuid'),
                $request->post('parentUuid'),
                $request->post('accountDomain'),
                $request->post('dialAt'),
                $request->post('bridgeAt'),
                $request->post('lgDirection'),
                $request->post('serverTime'),
                $request->post('leg'),
                $request->post('leg2'),
                $request->post('otherLegs'),
                $request->post('trunkNum'),
                $request->post('trunkName')
            );

            $this->repository->put($event);

            return [];
        }
    }
}
