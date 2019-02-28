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
    public const CONTEXT = 'phonet\\job\\call\\complete\\receive';

    /** @var string */
    protected $uuid;

    /** @var \DateTimeInterface */
    protected $createdAt;

    /** @var \DateTimeInterface */
    protected $hangupAt;

    public function __construct(
        string $uuid,
        \DateTimeInterface $createdAt,
        \DateTimeInterface $hangupAt
    ) {
        $this->uuid = $uuid;
        $this->createdAt = $createdAt;
        $this->hangupAt = $hangupAt;
    }

    /**
     * @param \yii\queue\Queue $queue
     *
     * @throws Phonet\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     * @todo: If Phonet implement method that can help fetch call data by uuid - need refactor of logic!
     */
    public function execute($queue)
    {
        /** @var Phonet\Repository $repository */
        $repository = \Yii::$container->get(Phonet\Repository::class);
        $offset = 0;
        $calls = $this->getCalls($repository, $offset);

        while (!empty($calls)) {
            $needCall = $this->fetchNeedCall($calls);

            if (!$needCall and \count($calls) <= 50) {
                $offset += 50;
                $calls = $this->getCalls($repository, $offset);
            } else {
                $completeCallData = new Phonet\Yii\Record\Call\Complete([
                    'uuid' => $needCall->getUuid(),
                    'transfer_history' => $needCall->getTransferHistory(),
                    'status' => $needCall->getStatus()->getKey(),
                    'duration' => $needCall->getDuration(),
                    'bill_secs' => $needCall->getBillSecs(),
                    'trunk' => $needCall->getTrunk(),
                    'audio_rec_url' => $needCall->getAudioRecUrl(),
                    'end_at' => $needCall->getEndAt()->toDateTimeString(),
                    'subject_number' => $needCall->getSubjectNumber(),
                    'subject_name' => $needCall->getSubjectName(),
                ]);

                if (!$completeCallData->save()) {
                    \Yii::error(
                        "Failed save complete call data. Errors: " . implode(
                            '; ',
                            $completeCallData->getErrorSummary(true)
                        ),
                        static::CONTEXT
                    );
                }

                break;
            }
        }
    }

    /**
     * @param Phonet\Repository $repository
     * @param int $offset
     *
     * @return Phonet\Call\Complete\Collection
     * @throws Phonet\Exception
     */
    protected function getCalls(Phonet\Repository $repository, $offset = 0): Phonet\Call\Complete\Collection
    {
        $from = Carbon::make($this->createdAt)->subHour();
        $to = Carbon::make($this->hangupAt)->addHour();
        $directions = new Phonet\Call\Direction\Collection([
            Phonet\Call\Direction::INTERNAL(),
            Phonet\Call\Direction::OUT(),
            Phonet\Call\Direction::IN(),
        ]);

        try {
            return $repository->companyCalls(
                $from,
                $to,
                $directions,
                50,
                $offset
            );
        } catch (Phonet\Exception $exception) {
            \Yii::error($exception->getMessage(), static::CONTEXT);

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
