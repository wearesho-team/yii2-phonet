<?php

namespace Wearesho\Phonet\Yii\Tests\Mock;

use Wearesho\Phonet;

/**
 * Class User
 * @package Wearesho\Phonet\Yii\Tests\Mock
 */
class User extends \yii\web\User implements Phonet\Yii\IdentityInterface, \yii\web\IdentityInterface
{
    public function getName(): string
    {
        return 'name';
    }

    public function getResponsibleEmployeeEmail(): ?string
    {
        return 'email';
    }

    public function getResponsibleEmployeeInternalNumber(): ?string
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

    public static function findBy(string $number, ?string $trunk): ?Phonet\Yii\IdentityInterface
    {
        return new static(['identityClass' => static::class]);
    }

    public function validateAuthKey($authKey)
    {
        // nothing
    }

    public function getId(): int
    {
        return 1;
    }

    public function getAuthKey()
    {
        return 'key';
    }

    public static function findIdentity($id)
    {
        return new static();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return new static();
    }
}
