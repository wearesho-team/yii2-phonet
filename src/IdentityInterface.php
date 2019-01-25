<?php

namespace Wearesho\Phonet\Yii;

use Wearesho\Phonet\Yii\Model\Client;

/**
 * Interface IdentityInterface
 * @package Wearesho\Phonet\Yii
 */
interface IdentityInterface
{
    public static function findBy(string $number, ?string $request, ?string $trunk): ?IdentityInterface;

    public function getName(): ?string;

    public function getUrl(): string;

    public function getUrlText(): string;

    public function getResponsibleEmployeeExt(): ?string;

    public function getResponsibleEmployeeEmail(): ?string;
}
