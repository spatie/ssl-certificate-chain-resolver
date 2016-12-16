<?php

namespace Spatie\CertificateChain\Test;

use Spatie\CertificateChain\Certificate;
use Spatie\CertificateChain\CertificateChain;

class CertifcateChainTest extends TestCase
{
    /** @test */
    public function it_can_resolve_a_certificate_chain()
    {
        $inputFile = __DIR__.'/fixtures/google/certificate.crt';

        $certificate = Certificate::loadFromFile($inputFile);

        $chainContents = CertificateChain::fetchForCertificate($certificate);

        $this->assertEquals(
            $this->sanitize(file_get_contents(__DIR__.'/fixtures/google/certificateChain.crt')),
            $this->sanitize($chainContents)
        );
    }
}
