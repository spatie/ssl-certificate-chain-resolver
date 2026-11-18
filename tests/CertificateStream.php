<?php

namespace Spatie\CertificateChain\Test;

use RuntimeException;

class CertificateStream
{
    public $context;

    public static array $responses = [];

    protected string $contents;

    protected int $position = 0;

    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        $this->contents = self::$responses[$path] ?? throw new RuntimeException("Unexpected certificate URL: {$path}");

        return true;
    }

    public function stream_read(int $count): string
    {
        $contents = substr($this->contents, $this->position, $count);
        $this->position += strlen($contents);

        return $contents;
    }

    public function stream_eof(): bool
    {
        return $this->position >= strlen($this->contents);
    }

    public function stream_stat(): array
    {
        return [];
    }
}
