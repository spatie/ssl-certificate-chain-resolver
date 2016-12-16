<?php

namespace Spatie\CertificateChain\Test;

use PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    protected function assertFileContentsEqual($fileA, $fileB)
    {
        $this->assertEquals(
            $this->sanitize(file_get_contents($fileA)),
            $this->sanitize(file_get_contents($fileB))
        );
    }

    protected function unlinkIfExist(string $file)
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    protected function sanitize(string $text)
    {
        return str_replace("\r", '', $text);
    }
}
