<?php

namespace Spatie\CertificateChain\Exceptions;

use Exception;

class CouldNotLoadCertificate extends Exception
{
    public static function cannotGetContents(string $file = '')
    {
        return new static("Could not load certificate from file $file.");
    }

    public static function invalidCertificateUrl(string $url = '')
    {
        return new static("$url Is not a valid url.");
    }
}
