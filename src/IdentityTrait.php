<?php

namespace Wearesho\Phonet\Yii;

/**
 * Trait IdentityTrait
 * @package Wearesho\Phonet\Yii
 * @codeCoverageIgnore
 */
trait IdentityTrait
{
    /** @var string|null */
    protected $name;

    /** @var string */
    protected $url;

    /** @var string */
    protected $urlText;

    /** @var string|null */
    protected $responsibleEmployeeInternalNumber;

    /** @var string|null */
    protected $responsibleEmployeeEmail;

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlText(): string
    {
        return $this->urlText;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponsibleEmployeeInternalNumber(): ?string
    {
        return $this->responsibleEmployeeInternalNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponsibleEmployeeEmail(): ?string
    {
        return $this->responsibleEmployeeEmail;
    }
}
