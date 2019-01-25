<?php

namespace Wearesho\Phonet\Yii\Panel;

use Horat1us\Yii\Validators\InstanceValidator;
use Wearesho\Phonet;
use Wearesho\Yii;
use yii\filters;
use yii\web\HttpException;

/**
 * Class HandlerPanel
 * @package Wearesho\Phonet\Yii\Panel
 */
class HandlerPanel extends Yii\Http\Panel
{
    /** @var Phonet\Yii\IdentityInterface */
    public $identity = Phonet\Yii\IdentityInterface::class;

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

    public function rules()
    {
        return [
            ['identity', 'required',],
            ['identity', InstanceValidator::class,]
        ];
    }

    /**
     * @return array
     * @throws HttpException
     */
    public function generateResponse(): array
    {
        $isCallEvent = $this->fetch('event') ?? false;

        if (!$isCallEvent) {
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
