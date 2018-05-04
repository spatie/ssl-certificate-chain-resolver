<?php

namespace Spatie\CertificateChain\Test;

use Spatie\CertificateChain\Certificate;
use Spatie\CertificateChain\CertificateChain;

class CertifcateChainTest extends TestCase
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
        $inputFile = __DIR__."/fixtures/{$certificateType}/certificate.crt";

        $certificate = Certificate::loadFromFile($inputFile);

        $chainContents = CertificateChain::fetchForCertificate($certificate);

        $this->assertEquals(
            $this->sanitize(file_get_contents(__DIR__."/fixtures/{$certificateType}/certificateChain.crt")),
            $this->sanitize($chainContents)
        );
    }

    /** @test */
    public function it_throws_exception_on_invalid_certificate_chain()
    {
        $this->expectException(\Spatie\CertificateChain\Exceptions\CouldNotCreateCertificate::class);
        $this->expectExceptionMessage('Could not create a certificate with content `invalid_content`.');

        new Certificate('invalid_content');
    }

    public function certificateTypeProvider(): array
    {
        return [
           ['dv-google'],
           //['letsencrypt'],
           // ['ev-coolblue'],
        ];
    }
}
