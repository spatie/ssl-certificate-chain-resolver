<?php

namespace Spatie\CertificateChain\Test;

use Spatie\CertificateChain\Certificate;
use Spatie\CertificateChain\CertificateChain;

class CertificateChainTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider certificateTypeProvider
     *
     * @param string $certificateType
     */
    public function it_can_fetch_a_certificate_chain(string $certificateType)
    {
        $inputFile = __DIR__ . "/fixtures/{$certificateType}/certificate.crt";

        $certificate = Certificate::loadFromFile($inputFile);

        $chainContents = CertificateChain::fetchForCertificate($certificate);

        $this->assertEquals(
            $this->sanitize(file_get_contents(__DIR__ . "/fixtures/{$certificateType}/certificateChain.crt")),
            $this->sanitize($chainContents)
        );
    }

    public function certificateTypeProvider(): array
    {
        return [
           // ['dv-google'],
           ['letsencrypt'],
           // ['ev-coolblue'],
        ];
    }
}