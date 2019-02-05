<?php

namespace Wearesho\Phonet\Yii\Data;

use Wearesho\Phonet\Data;
use yii\base;

/**
 * Class Subject
 * @package Wearesho\Phonet\Yii\Data
 *
 * @property string|null $id
 * @property string|null $name;
 * @property string $number
 * @property string|null $company
 * @property string $uri
 * @property string|null $priority
 */
class Subject extends base\Model implements Data\SubjectInterface
{
    use Data\SubjectTrait;

    public function __construct(
        string $number,
        string $uri,
        string $id = null,
        string $name = null,
        string $company = null,
        string $priority = null,
        array $config = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->number = $number;
        $this->company = $company;
        $this->uri = $uri;
        $this->priority = $priority;

        parent::__construct($config);
    }
}
