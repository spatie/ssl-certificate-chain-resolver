<?php

use Codeception\Specify;
use Spatie\Certificate\Certificate;

class CertificateTest extends \Codeception\TestCase\Test
{
    use Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    // tests
    public function testCertificate()
    {
        $certificateContents = '-----BEGIN CERTIFICATE-----
MIIDITCCAoqgAwIBAgIQT52W2WawmStUwpV8tBV9TTANBgkqhkiG9w0BAQUFADBM
MQswCQYDVQQGEwJaQTElMCMGA1UEChMcVGhhd3RlIENvbnN1bHRpbmcgKFB0eSkg
THRkLjEWMBQGA1UEAxMNVGhhd3RlIFNHQyBDQTAeFw0xMTEwMjYwMDAwMDBaFw0x
MzA5MzAyMzU5NTlaMGgxCzAJBgNVBAYTAlVTMRMwEQYDVQQIEwpDYWxpZm9ybmlh
MRYwFAYDVQQHFA1Nb3VudGFpbiBWaWV3MRMwEQYDVQQKFApHb29nbGUgSW5jMRcw
FQYDVQQDFA53d3cuZ29vZ2xlLmNvbTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkC
gYEA3rcmQ6aZhc04pxUJuc8PycNVjIjujI0oJyRLKl6g2Bb6YRhLz21ggNM1QDJy
wI8S2OVOj7my9tkVXlqGMaO6hqpryNlxjMzNJxMenUJdOPanrO/6YvMYgdQkRn8B
d3zGKokUmbuYOR2oGfs5AER9G5RqeC1prcB6LPrQ2iASmNMCAwEAAaOB5zCB5DAM
BgNVHRMBAf8EAjAAMDYGA1UdHwQvMC0wK6ApoCeGJWh0dHA6Ly9jcmwudGhhd3Rl
LmNvbS9UaGF3dGVTR0NDQS5jcmwwKAYDVR0lBCEwHwYIKwYBBQUHAwEGCCsGAQUF
BwMCBglghkgBhvhCBAEwcgYIKwYBBQUHAQEEZjBkMCIGCCsGAQUFBzABhhZodHRw
Oi8vb2NzcC50aGF3dGUuY29tMD4GCCsGAQUFBzAChjJodHRwOi8vd3d3LnRoYXd0
ZS5jb20vcmVwb3NpdG9yeS9UaGF3dGVfU0dDX0NBLmNydDANBgkqhkiG9w0BAQUF
AAOBgQAhrNWuyjSJWsKrUtKyNGadeqvu5nzVfsJcKLt0AMkQH0IT/GmKHiSgAgDp
ulvKGQSy068Bsn5fFNum21K5mvMSf3yinDtvmX3qUA12IxL/92ZzKbeVCq3Yi7Le
IOkKcGQRCMha8X2e7GmlpdWC1ycenlbN0nbVeSv3JUMcafC4+Q==
-----END CERTIFICATE-----'.PHP_EOL;

        $certificate = new Certificate($certificateContents);

        $this->specify("it can get the contents of a certificate", function () use ($certificate, $certificateContents) {
            $this->assertSame(str_replace("\r", "", $certificate->getContents()), str_replace("\r", "", $certificateContents));
        });

        $this->specify("it can determine if the certificate has a parent", function () use ($certificate, $certificateContents) {
            $this->assertEquals($certificate->hasParentInTrustChain(), true);
        });

        $this->specify("it can determine the issuer DN of a certificate", function () use ($certificate, $certificateContents) {
            $this->assertEquals($certificate->getIssuerDN(), 'C=ZA, O=Thawte Consulting (Pty) Ltd., CN=Thawte SGC CA');
        });

        $this->specify("it can determine the URL of the parent certificate", function () use ($certificate, $certificateContents) {
            $this->assertEquals($certificate->getParentCertificateURL(), 'http://www.thawte.com/repository/Thawte_SGC_CA.crt');
        });
    }
}
