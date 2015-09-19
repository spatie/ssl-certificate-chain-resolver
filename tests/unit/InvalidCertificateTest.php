<?php

use Codeception\Specify;
use Spatie\Certificate\Certificate;

class InvalidCertificateTest extends \Codeception\TestCase\Test
{
    use Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testInvalidCertificate()
    {
        $this->specify('It throws the correct exception if the given inputfile is invalid.', function () {
            $this->setExpectedException('Exception', 'Invalid inputfile given.');
            new Certificate('Invalid Content fluff fluff fluff');
        });
    }
}
