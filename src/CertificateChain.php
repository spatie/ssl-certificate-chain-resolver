<?php

namespace Spatie\CertificateChain;

class CertificateChain
{
    protected $certificates;

    public static function fetchForCertificate(Certificate $certificate) {
        $certificateChain = new static($certificate);

        return $certificateChain->getContentOfCompleteChain();
    }

    public function __construct(Certificate $certificate)
    {
        $this->certificates = collect()->push($certificate);
    }

    public function getContentOfCompleteChain()
    {
        while($this->lastCertificate()->hasParentInTrustChain()) {
            $this->certificates->push($this->lastCertificate()->fetchParentCertificate());
        }

        return $this->certificates->map(function(Certificate $certificate) {
           return $certificate->getContents();
        })->implode('');
    }

    protected function lastCertificate(): Certificate
    {
        return $this->certificates->last();
    }
}
