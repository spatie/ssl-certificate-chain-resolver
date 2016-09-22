<?php
namespace Spatie\Certificate;

use Exception;
use phpseclib\File\X509;

class Certificate
{
    /**
     * @param string The contents of the certificate
     */
    protected $contents;

    public function __construct($contents)
    {
        $this->guardAgainstInvalidContents($contents);

        $this->contents = $contents;
    }

    /**
     * Get the URL of the parent certificate.
     *
     * @return string
     */
    public function getParentCertificateURL()
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

    /**
     * Does this certificate have a parent.
     *
     * @return bool
     */
    public function hasParentInTrustChain()
    {
        return (! $this->getParentCertificateURL() == '');
    }

    /**
     * Get the contents of the certificate.
     *
     * @return string
     */
    public function getContents()
    {
        $x509 = new X509();

        return $x509->saveX509($x509->loadX509($this->contents)).PHP_EOL;
    }

    /**
     * Get the issuer DN of the certificate.
     *
     * @return string
     */
    public function getIssuerDN()
    {
        $x509 = new X509();
        $x509->loadX509($this->contents);

        return $x509->getIssuerDN(X509::DN_STRING);
    }

    /**
     * Check if inputfile is correct.
     *
     * @param $contents
     *
     * @throws Exception
     */
    protected function guardAgainstInvalidContents($contents)
    {
        $x509 = new X509();

        if (!$x509->loadX509($contents)) {
            throw new Exception('Invalid inputfile given.');
        }
    }
}
