<?php

use Overtrue\Validation\Factory;

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    protected $validator;

    public function setUp()
    {
        $this->validator = new Factory();
    }

    public function testConfirmed()
    {
        $validator = $this->validator->make(['password' => 'foo'], ['password' => 'confirmed']);

        $this->assertSame('The password confirmation does not match.', $validator->messages()[0]);
        $this->assertTrue($validator->fails());
    }
}