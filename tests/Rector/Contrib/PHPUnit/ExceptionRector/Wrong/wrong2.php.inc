<?php declare(strict_types=1);

final class MyTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $this->setExpectedExceptionRegExp('SomeException', 'SomREGEPX');
    }
}
