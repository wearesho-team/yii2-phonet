<?php

namespace Wearesho\Phonet\Yii\Data;

use Wearesho\Phonet\Data;
use yii\base;

/**
 * Class Employee
 * @package Wearesho\Phonet\Yii\Data
 *
 * @property int $id
 * @property string $internalNumber
 * @property string $displayName
 */
class Employee extends base\Model implements Data\BaseEmployeeInterface
{
    use Data\BaseEmployeeTrait;

    public function __construct(int $id, string $internalNumber, string $displayName, array $config = [])
    {
        parent::__construct($config);

        $this->id = $id;
        $this->internalNumber = $internalNumber;
        $this->displayName = $displayName;
    }
}
