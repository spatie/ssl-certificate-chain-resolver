<?php

namespace Spatie\CertificateChain\Exceptions;

class CouldNotCreateCertificate extends \Exception
{
    public static function invalidContent(string $content = '')
    {
        return new static("Could not create a certificate with content `{$content}`.");
    }
}