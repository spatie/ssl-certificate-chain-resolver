<?php

namespace Spatie\CertificateChain;

use PHPUnit_Framework_TestCase;

class ResolveCommandTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_resolve_a_certificate_chain()
    {
        $inputFile = __DIR__.'/fixtures/google/certificate.crt';

        $outputFile = __DIR__.'/temp/certificateChain.crt';

        unlink($outputFile);

        exec("php ./ssl-certificate-chain-resolver resolve {$inputFile} {$outputFile}");

        $certificateChain = __DIR__.'/fixtures/google/certificateChain.crt';

        $this->assertFileContentsEqual($certificateChain, $outputFile);
    }

    protected function assertFileContentsEqual($fileA, $fileB)
    {
        $this->assertEquals(
            $this->sanitize(file_get_contents($fileA)),
            $this->sanitize(file_get_contents($fileB))
        );
    }

    protected function sanitize(string $text)
    {
        return str_replace("\r", '', $text);
    }
}
