<?php

namespace Spatie\CertificateChain\Test;

class ResolveCommandTest extends TestCase
{
    /** @test */
    public function it_can_resolve_a_certificate_chain()
    {
        $inputFile = __DIR__ . '/fixtures/dv-google/certificate.crt';

        $outputFile = __DIR__ . '/temp/certificateChain.crt';

        $this->unlinkIfExist($outputFile);

        exec("php ./ssl-certificate-chain-resolver resolve {$inputFile} {$outputFile}");

        $certificateChain = __DIR__ . '/fixtures/dv-google/certificateChain.crt';

        $this->assertFileContentsEqual($certificateChain, $outputFile);
    }
}