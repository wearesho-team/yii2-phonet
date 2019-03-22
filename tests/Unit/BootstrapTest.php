<?php

namespace Wearesho\Phonet\Yii\Tests\Unit;

use Wearesho\Phonet\Repository;
use Wearesho\Phonet\Yii\Bootstrap;

/**
 * Class BootstrapTest
 * @package Wearesho\Phonet\Yii\Tests\Unit
 */
class BootstrapTest extends TestCase
{
    /** @var array */
    protected $aliases;

    protected function setUp(): void
    {
        parent::setUp();

        $this->aliases = \Yii::$aliases;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        \Yii::$aliases = $this->aliases;
    }

    public function testBootstrapApp(): void
    {
        $bootstrap = new Bootstrap();

        $bootstrap->bootstrap(\Yii::$app);

        $this->assertEquals(
            \Yii::getAlias('@vendor/wearesho-team/yii2-phonet/src'),
            \Yii::getAlias('@Wearesho/Phonet/Yii')
        );
        $this->assertTrue(\Yii::$container->has(Repository::class));
    }
}
