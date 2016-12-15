<?php

namespace Spatie\CertificateChain;

use phpseclib\File\X509;
use Spatie\CertificateChain\Exceptions\CouldNotCreateCertificate;

class Certificate
{
    /**
     * @param string The contents of the certificate
     */
    protected $contents;

    /**
     * @param string $inputFile
     *
     * @return static
     */
    public static function loadFromFile(string $inputFile)
    {
        return new static(file_get_contents($inputFile));
    }

    /**
     * @param string $url
     *
     * @return static
     */
    public static function loadFromUrl(string $url)
    {
        return static::loadFromFile($url);
    }

    public function __construct(string $contents)
    {
        $this->guardAgainstInvalidContents($contents);

        $this->contents = $contents;
    }

    /**
     * Get the URL of the parent certificate.
     *
     * @return string
     */
    public function getParentCertificateUrl()
    {
        $x509 = new X509();

        $certProperties = $x509->loadX509($this->contents);

        foreach ($certProperties['tbsCertificate']['extensions'] as $extension) {
            if ($extension['extnId'] == 'id-pe-authorityInfoAccess') {
                foreach ($extension['extnValue'] as $extnValue) {
                    if ($extnValue['accessMethod'] == 'id-ad-caIssuers') {
                        return $extnValue['accessLocation']['uniformResourceIdentifier'];
                    }
                }
            }
        }

        return '';
    }

    public function fetchParentCertificate(): Certificate
    {
        return static::loadFromUrl($this->getParentCertificateUrl());
    }

    public function hasParentInTrustChain(): bool
    {
        return ! $this->getParentCertificateUrl() == '';
    }

    public function getContents(): string
    {
        $x509 = new X509();

        return $x509->saveX509($x509->loadX509($this->contents)).PHP_EOL;
    }

    protected function guardAgainstInvalidContents(string $content)
    {

        if (! (new X509())->loadX509($content)) {

        //    throw CouldNotCreateCertificate::invalidContent($content);
        }
    }
}
