<?php
namespace Spatie\Certificate;

class CertificateFactory
{
    /**
     * Create the certificate with the given contents.
     *
     * @param $contents
     *
     * @return Certificate
     */
    public static function create($contents)
    {
        return new Certificate($contents);
    }
}
