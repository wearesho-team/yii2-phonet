<?php

namespace Wearesho\Phonet\Yii\Job;

use Carbon\Carbon;
use Wearesho\Phonet;
use yii\queue\JobInterface;

/**
 * Class ReceiveCompleteCall
 * @package Wearesho\Phonet\Yii\Hangup
 */
class ReceiveCompleteCall implements JobInterface
{
    /** @var Phonet\Repository */
    protected $repository;

    /** @var string */
    protected $uuid;

    /** @var \DateTimeInterface */
    protected $createdAt;

    /** @var \DateTimeInterface */
    protected $hangupAt;

    public function __construct(
        Phonet\Repository $repository,
        string $uuid,
        \DateTimeInterface $createdAt,
        \DateTimeInterface $hangupAt
    ) {
        $this->repository = $repository;
        $this->uuid = $uuid;
        $this->createdAt = $createdAt;
        $this->hangupAt = $hangupAt;
    }

    // todo: If Phonet implement method that can help fetch call data by uuid - need refactor of logic!
    public function execute($queue)
    {
        $offset = 0;
        $calls = $this->getCalls($offset);

        while (!empty($calls)) {
            /** @var Phonet\Data\CompleteCall[]|array $needCall */
            $needCall = \array_filter($calls->getArrayCopy(), function (Phonet\Data\CompleteCall $call): bool {
                return $call->getUuid() === $this->uuid;
            });

            if (empty($needCall) and \count($calls) <= 50) {
                $offset += 50;
                $calls = $this->getCalls($offset);
            } else {
                $needCall = \array_shift($needCall); // shift find unique call from array

                $completeCallData = new Phonet\Yii\Record\CompleteCallData([
                    'uuid' => $needCall->getUuid(),
                    'transfer_history' => $needCall->getTransferHistory(),
                    'status' => $needCall->getStatus(),
                    'duration' => $needCall->getDuration(),
                    'bill_secs' => $needCall->getBillSecs(),
                    'trunk' => $needCall->getTrunk(),
                    'audio_rec_url' => $needCall->getAudioRecUrl(),
                ]);
                $completeCallData->save();
            }
        }
    }

    protected function getCalls($offset = 0): Phonet\Data\Collection\CompleteCall
    {
        $from = Carbon::make($this->createdAt)->subHour();
        $to = Carbon::make($this->hangupAt)->addHour();
        $directions = new Phonet\Data\Collection\Direction([
            Phonet\Enum\Direction::OUT(),
            Phonet\Enum\Direction::IN(),
        ]);
        return $this->repository->companyCalls(
            $from,
            $to,
            $directions,
            50,
            $offset
        );
    }
}
