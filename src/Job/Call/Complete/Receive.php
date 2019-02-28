<?php

namespace Wearesho\Phonet\Yii\Job\Call\Complete;

use Carbon\Carbon;
use Wearesho\Phonet;
use yii\queue\JobInterface;

/**
 * Class Receive
 * @package Wearesho\Phonet\Yii\Job\Call\Complete
 */
class Receive implements JobInterface
{
    public const CONTEXT = 'phonet\\job\\receiveCall';

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

    /**
     * @param \yii\queue\Queue $queue
     *
     * @throws Phonet\Exception
     * @todo: If Phonet implement method that can help fetch call data by uuid - need refactor of logic!
     */
    public function execute($queue)
    {
        $offset = 0;
        $calls = $this->getCalls($offset);

        while (!empty($calls)) {
            $needCall = $this->fetchNeedCall($calls);

            if (!$needCall and \count($calls) <= 50) {
                $offset += 50;
                $calls = $this->getCalls($offset);
            } else {
                $completeCallData = new Phonet\Yii\Record\Call\Complete\Data([
                    'uuid' => $needCall->getUuid(),
                    'transfer_history' => $needCall->getTransferHistory(),
                    'status' => $needCall->getStatus(),
                    'duration' => $needCall->getDuration(),
                    'bill_secs' => $needCall->getBillSecs(),
                    'trunk' => $needCall->getTrunk(),
                    'audio_rec_url' => $needCall->getAudioRecUrl(),
                ]);

                if (!$completeCallData->save()) {
                    \Yii::error(
                        "Failed save complete call data. Errors: " . implode(
                            '; ',
                            $completeCallData->getErrorSummary(true)
                        ),
                        [static::CONTEXT]
                    );
                }

                break;
            }
        }
    }

    /**
     * @param int $offset
     *
     * @return Phonet\Call\Complete\Collection
     * @throws Phonet\Exception
     */
    protected function getCalls($offset = 0): Phonet\Call\Complete\Collection
    {
        $from = Carbon::make($this->createdAt)->subHour();
        $to = Carbon::make($this->hangupAt)->addHour();
        $directions = new Phonet\Call\Direction\Collection([
            Phonet\Call\Direction::INTERNAL(),
            Phonet\Call\Direction::OUT(),
            Phonet\Call\Direction::IN(),
        ]);

        try {
            return $this->repository->companyCalls(
                $from,
                $to,
                $directions,
                50,
                $offset
            );
        } catch (Phonet\Exception $exception) {
            \Yii::error($exception->getMessage(), [static::CONTEXT]);

            throw $exception;
        }
    }

    protected function fetchNeedCall(Phonet\Call\Complete\Collection $calls): ?Phonet\Call\Complete
    {
        $needCall = \array_filter($calls->getArrayCopy(), function (Phonet\Call\Complete $call): bool {
            return $call->getUuid() === $this->uuid;
        });

        return $needCall ? \array_shift($needCall) : null;
    }
}
