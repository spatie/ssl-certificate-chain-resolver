<?php

use Spatie\CertificateChain\Certificate;
use Spatie\CertificateChain\CertificateChain;
use Spatie\CertificateChain\Exceptions\CouldNotCreateCertificate;
use Spatie\CertificateChain\Exceptions\CouldNotLoadCertificate;

it('can fetch a certificate chain', function (string $certificateType) {
    $certificate = Certificate::loadFromFile(__DIR__."/fixtures/{$certificateType}/certificate.crt");

    $chainContents = CertificateChain::fetchForCertificate($certificate);

    expect(str_replace("\r", '', $chainContents))
        ->toBe(str_replace("\r", '', file_get_contents(__DIR__."/fixtures/{$certificateType}/certificateChain.crt")));
})->with(['dv-google', 'letsencrypt', 'ev-coolblue']);

it('rejects invalid certificate contents', function (string $contents) {
    expect(fn () => new Certificate($contents))
        ->toThrow(CouldNotCreateCertificate::class, "Could not create a certificate with content `{$contents}`.");
})->with(['invalid_content', '']);

it('reports an unreadable certificate file', function () {
    set_error_handler(fn () => true);

    try {
        expect(fn () => Certificate::loadFromFile(__DIR__.'/fixtures/missing.crt'))
            ->toThrow(CouldNotLoadCertificate::class);
    } finally {
        restore_error_handler();
    }
});

it('loads DER certificates', function () {
    $pem = $this->certificateContents('dv-google');
    $der = base64_decode(preg_replace('/-----[^-]+-----|\s/', '', $pem), true);

    expect((new Certificate($der))->getContents())->toBe((new Certificate($pem))->getContents());
});

it('loads PKCS7 certificates', function () {
    $pem = $this->certificateContents('dv-google');
    $pkcs7 = file_get_contents(__DIR__.'/fixtures/dv-google/certificate.p7c');

    expect((new Certificate($pkcs7))->getContents())->toBe((new Certificate($pem))->getContents());
});

it('finds the parent certificate URL', function () {
    $certificate = new Certificate($this->certificateContents('dv-google'));

    expect($certificate->getParentCertificateUrl())
        ->toBe('http://www.thawte.com/repository/Thawte_SGC_CA.crt')
        ->and($certificate->hasParentInTrustChain())->toBeTrue();
});

it('stops at a certificate without a parent URL', function () {
    $certificate = new Certificate($this->certificateContents('dv-google', 1));

    expect($certificate->getParentCertificateUrl())->toBe('')
        ->and($certificate->hasParentInTrustChain())->toBeFalse()
        ->and(CertificateChain::fetchForCertificate($certificate))->toBe($certificate->getContents());
});

it('rejects non HTTP parent certificate URLs', function (string $url) {
    $certificate = new class($url) extends Certificate {
        public function __construct(protected string $url)
        {
        }

        public function getParentCertificateUrl(): string
        {
            return $this->url;
        }
    };

    expect(fn () => $certificate->fetchParentCertificate())
        ->toThrow(CouldNotLoadCertificate::class, "{$url} Is not a valid url.");
})->with(['file:///etc/passwd', 'data:text/plain,certificate', 'ftp://example.com/issuer.crt', '']);
