<?php

namespace Wearesho\Phonet\Yii\Tests\Unit\Record;

use Wearesho\Phonet\Yii\Record\Employee;
use Wearesho\Phonet\Yii\Tests\Unit\TestCase;

/**
 * Class EmployeeTest
 * @package Wearesho\Phonet\Yii\Tests\Unit\Record
 */
class EmployeeTest extends TestCase
{
    protected const ID = 100;
    protected const DISPLAY_NAME = 'test-display-name';
    protected const INTERNAL_NUMBER = 'test-internal-number';

    /** @var Employee */
    protected $fakeEmployee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fakeEmployee = $this->createEmployee();
    }

    public function testGetDisplayName(): void
    {
        $this->assertEquals(static::DISPLAY_NAME, $this->fakeEmployee->display_name);
    }

    public function testGetInternalNumber(): void
    {
        $this->assertEquals(static::INTERNAL_NUMBER, $this->fakeEmployee->internal_number);
    }

    public function testGetId(): void
    {
        $this->assertEquals(static::ID, $this->fakeEmployee->id);
    }

    protected function createEmployee(): Employee
    {
        $employee = new Employee([
            'display_name' => static::DISPLAY_NAME,
            'internal_number' => static::INTERNAL_NUMBER,
            'id' => static::ID,
        ]);
        $this->assertTrue($employee->save());

        return $employee;
    }
}
