<?php

namespace Spatie\CertificateChain\Test;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        CertificateStream::$responses = [
            'http://www.thawte.com/repository/Thawte_SGC_CA.crt' => $this->certificateContents('dv-google', 1),
            'http://cert.int-x3.letsencrypt.org/' => $this->certificateContents('letsencrypt', 1),
            'http://apps.identrust.com/roots/dstrootcax3.p7c' => $this->certificateContents('letsencrypt', 2),
            'http://crt.comodoca.com/COMODORSAExtendedValidationSecureServerCA.crt' => $this->certificateContents('ev-coolblue', 1),
            'http://crt.comodoca.com/COMODORSAAddTrustCA.crt' => $this->certificateContents('ev-coolblue', 2),
        ];

        foreach (['http', 'https'] as $scheme) {
            stream_wrapper_unregister($scheme);
            stream_wrapper_register($scheme, CertificateStream::class);
        }
    }

    protected function tearDown(): void
    {
        foreach (['http', 'https'] as $scheme) {
            stream_wrapper_restore($scheme);
        }

        foreach (glob(__DIR__.'/temp/*') as $file) {
            unlink($file);
        }

        parent::tearDown();
    }

    protected function certificateContents(string $type, int $index = 0): string
    {
        preg_match_all(
            '/-----BEGIN CERTIFICATE-----.*?-----END CERTIFICATE-----/s',
            file_get_contents(__DIR__."/fixtures/{$type}/certificateChain.crt"),
            $matches,
        );

        return $matches[0][$index]."\n";
    }
}
