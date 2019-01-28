<?php

namespace Wearesho\Phonet\Yii\Tests\Mock;

use Wearesho\Phonet\Yii\IdentityInterface;

/**
 * Class User
 * @package Wearesho\Phonet\Yii\Tests\Mock
 */
class User implements IdentityInterface
{
    public function getName(): ?string
    {
        return 'name';
    }

    public function getResponsibleEmployeeEmail(): ?string
    {
        return 'email';
    }

    public function getResponsibleEmployeeExt(): ?string
    {
        return 'number';
    }

    public function getUrl(): string
    {
        return 'url';
    }

    public function getUrlText(): string
    {
        return 'url-text';
    }

    public static function findBy(string $number, ?string $request, ?string $trunk): ?IdentityInterface
    {
        return new User();
    }
}
