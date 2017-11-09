<?php

namespace Spatie\CertificateChain\Test;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
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
