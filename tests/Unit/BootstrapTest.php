<?php

namespace Wearesho\Phonet\Yii\Tests\Unit;

use Wearesho\Phonet\Yii\Bootstrap;
use yii\console\Application;

/**
 * Class BootstrapTest
 * @package Wearesho\Phonet\Yii\Tests\Unit
 */
class BootstrapTest extends TestCase
{
    public function testBootstrapApp(): void
    {
        $bootstrap = new Bootstrap();

        $bootstrap->bootstrap(\Yii::$app);

        $this->assertEquals(
            \Yii::getAlias('@vendor/wearesho-team/yii2-phonet/src'),
            \Yii::getAlias('@Wearesho\Phonet\Yii')
        );
    }
}
