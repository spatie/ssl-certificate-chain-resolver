<?php

namespace Spatie\CertificateChain;

use phpseclib\File\ASN1;
use phpseclib\File\X509;
use Spatie\CertificateChain\Exceptions\CouldNotLoadCertificate;
use Spatie\CertificateChain\Exceptions\CouldNotCreateCertificate;

class Certificate
{
    /**
     * @param string
     */
    protected $contents;

    /**
     * @param string $inputFile
     *
     * @return static
     */
    public static function loadFromFile(string $inputFile)
    {
        $contents = @file_get_contents($inputFile);

        if ($contents === false) {
            throw CouldNotLoadCertificate::cannotGetContents($inputFile);
        }

        return new static($contents);
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
        // If we are missing the pem certificate header, try to convert it to a pem format first
        if (!empty($contents) && strpos($contents, '-----BEGIN CERTIFICATE-----') === false) {
            // Extract from either a PKCS#7 format or DER formatted contents
            $contents = self::convertPkcs72Pem($contents) ?? self::convertDer2Pem($contents);
        }

        $this->guardAgainstInvalidContents($contents);

        $this->contents = $contents;
    }

    /**
     * Get the URL of the parent certificate.
     */
    public function getParentCertificateUrl(): string
    {
        $x509 = new X509();

        $certProperties = $x509->loadX509($this->contents);

        if (empty($certProperties['tbsCertificate']['extensions'])) {
            return '';
        }

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
            throw CouldNotCreateCertificate::invalidContent($content);
        }
    }

    protected function convertPkcs72Pem(string $pkcs7)
    {
        $asn     = new ASN1();
        $decoded = $asn->decodeBER($pkcs7);
        $data    = $decoded[0]['content'] ?? [];

        // Make sure we are dealing with actual data
        if (empty($data)) {
            return null;
        }

        // Make sure this is an PKCS#7 signedData object
        if ($data[0]['type'] === ASN1::TYPE_OBJECT_IDENTIFIER && $data[0]['content'] === '1.2.840.113549.1.7.2') {
            // Loop over all the content in the signedData object
            foreach ($data[1]['content'] as $pkcs7SignedData) {
                // Find all sequences of data
                if ($pkcs7SignedData['type'] === ASN1::TYPE_SEQUENCE) {
                    // Extract the sequence identifier if possible
                    $identifier = $pkcs7SignedData['content'][2] ?? '';

                    // Make sure the sequence is a PKCS#7 data object we are dealing with
                    if ($identifier['type'] === ASN1::TYPE_SEQUENCE && $identifier['content'][0]['content'] === '1.2.840.113549.1.7.1') {
                        // Extract the certificate data
                        $certificate = $pkcs7SignedData['content'][3];

                        // Extract the raw certificate data from the PKCS#7 string
                        $rawCert = substr($pkcs7, $certificate['start'] + $certificate['headerlength'], $certificate['length'] - $certificate['headerlength']);

                        // Return the PEM encoded certificate
                        return $this->convertDer2Pem($rawCert);
                    }
                }
            }
        }

        return null;
    }

    protected function convertDer2Pem(string $der_data, $type = 'CERTIFICATE'): string
    {
        $pem = chunk_split(base64_encode($der_data), 64, "\n");

        return "-----BEGIN {$type}-----\n{$pem}-----END {$type}-----\n";
    }
}
