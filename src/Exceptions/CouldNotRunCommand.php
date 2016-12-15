<?php

namespace Spatie\CertificateChain\Exceptions;

class CouldNotRunCommand extends \Exception
{
    public static function inputFileDoesNotExist(string $inputFile = '')
    {
        return new static("The given inputfile `{$inputFile}` does not exist.");
    }
}
