<?php

namespace Wearesho\Phonet\Yii\Tests\Unit\Panel;

use PHPUnit\Framework\TestCase;
use Wearesho\Phonet\Yii;
use Wearesho\Yii\Http;
use yii\di\Container;

/**
 * Class HandlerPanelTest
 * @package Wearesho\Phonet\Yii\Tests\Unit\Panel
 */
class HandlerPanelTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        \Yii::$app = new \yii\console\Application([
            'id' => 'yii2-phonet',
            'basePath' => \Yii::getAlias('@Wearesho/Phonet/Yii'),
        ]);

        \Yii::$container = new Container();
        \Yii::$container->set(Yii\RepositoryInterface::class, Yii\Tests\Mock\Repository::class);
    }

    public function testExistClientName()
    {
        $_POST['otherLegNum'] = 'other-leg-num';

        $panel = new Yii\Panel\HandlerPanel(new Http\Request(), new Http\Response(), [
            'identity' => Yii\Tests\Mock\User::class,
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
            $panel->generateResponse()
        );
    }
}
