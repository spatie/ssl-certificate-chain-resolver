<?php

namespace Spatie\CertificateChain\Exceptions;

use Exception;

/** @phpstan-consistent-constructor */
class CouldNotRunCommand extends Exception
{
    public static function inputFileDoesNotExist(string $inputFile = '')
    {
        return new static("The given inputfile `{$inputFile}` does not exist.");
    }
}
