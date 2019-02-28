<?php

namespace Wearesho\Phonet\Yii\Tests\Unit\Job\Call\Complete;

use Carbon\Carbon;
use Wearesho\Phonet;
use yii\log\Logger;

/**
 * Class ReceiveTest
 * @package Wearesho\Phonet\Yii\Tests\Unit\Job\Call\Complete
 */
class ReceiveTest extends Phonet\Yii\Tests\Unit\TestCase implements Phonet\Tests\Unit\Api\TestCaseInterface
{
    use Phonet\Tests\Unit\Api\TestCaseTrait {
        setUp as protected PhonetSetUp;
    }

    protected const PARENT_UUID = 'parent-uuid';
    protected const UUID = 'uuid';

    /** @var Phonet\Repository */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        \Yii::setLogger(new Logger());
        $this->PhonetSetUp();

        $this->repository = new Phonet\Repository($this->sender);

        $operator = new Phonet\Yii\Record\Employee([
            'id' => 10,
            'internal_number' => 'number',
            'display_name' => 'display-name',
        ]);
        $this->assertTrue($operator->save(), implode($operator->getErrorSummary(true)));
        $call = new Phonet\Yii\Record\Call([
            'uuid' => static::UUID,
            'state' => Phonet\Call\Event::HANGUP,
            'domain' => static::DOMAIN,
            'dial_at' => Carbon::now()->toDateTimeString(),
            'type' => Phonet\Call\Direction::IN,
            'pause' => Phonet\Yii\Call\Pause::OFF,
            'operator_id' => $operator->id,
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
        $this->assertTrue($call->save(), implode($call->getErrorSummary(true)));
    }

    public function testSuccessFindInFirstBatch50(): void
    {
        $calls = $this->getBatchCompleteCallDataJson();
        $calls[49]['uuid'] = static::UUID;

        $this->mock->append(
            $this->getSuccessAuthResponse(static::SESSION_ID),
            $this->getSuccessRestResponse(\json_encode($calls))
        );

        $job = $this->createJob(static::UUID);

        $job->execute('queue');

        $completeCallData = Phonet\Yii\Record\Call\Complete\Data::find()->andWhere(['uuid' => static::UUID])->all();

        $this->assertCount(1, $completeCallData);
        $data = $completeCallData[0];

        $this->assertEquals(
            [
                'id' => 1,
                'uuid' => 'uuid',
                'transfer_history' => null,
                'status' => Phonet\Call\Complete\Status::TARGET_RESPONDED(),
                'duration' => 4,
                'bill_secs' => 3,
                'trunk' => null,
                'end_at' => null,
                'audio_rec_url' => 'https://podium.betell.com.ua/rest/public/calls/invalid-uuid',
                'subject_number' => null,
                'subject_name' => null,
            ],
            $data->attributes
        );
    }

    public function testGetCallsException(): void
    {
        $this->mock->append(
            $this->getSuccessAuthResponse(static::SESSION_ID),
            $this->getResponse(400, 'Unexpected exception')
        );

        $job = $this->createJob(static::UUID);

        $this->expectException(Phonet\Exception::class);
        $this->expectExceptionMessage('Api [rest/calls/company.api] failed');

        $job->execute('queue');
    }

    protected function createJob(string $uuid): Phonet\Yii\Job\Call\Complete\Receive
    {
        return new Phonet\Yii\Job\Call\Complete\Receive($this->repository, $uuid, Carbon::now(), Carbon::now());
    }

    protected function getBatchCompleteCallDataJson(): array
    {
        $datum = [
            "parentUuid" => static::PARENT_UUID,
            "uuid" => 'invalid-uuid',
            "endAt" => 1435319298470,
            "lgDirection" => 1,
            "otherLegNum" => null,
            "otherLegName" => null,
            "leg" => [
                "id" => 36,
                "type" => 1,
                "displayName" => "Васильев Андрей",
                "ext" => "001"
            ],
            "leg2" => [
                "id" => 19,
                "type" => 1,
                "displayName" => "Operator 4",
                "ext" => "004"
            ],
            "billSecs" => 3,
            "duration" => 4,
            "disposition" => 0,
            "transferHistory" => null,
            "audioRecUrl" => "https://podium.betell.com.ua/rest/public/calls/invalid-uuid",
            "trunk" => null
        ];

        return array_fill(0, 50, $datum);
    }
}
