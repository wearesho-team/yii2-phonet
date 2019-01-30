<?php

namespace Wearesho\Phonet\Yii;

/**
 * Class Identity
 * @package Wearesho\Phonet\Yii
 */
abstract class Identity implements IdentityInterface
{
    /** @var string|null */
    protected $name;

    /** @var string */
    protected $url;

    /** @var string */
    protected $urlText;

    /** @var string|null */
    protected $responsibleEmployeeExt;

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
    public function getResponsibleEmployeeExt(): ?string
    {
        return $this->responsibleEmployeeExt;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponsibleEmployeeEmail(): ?string
    {
        return $this->responsibleEmployeeEmail;
    }
}
