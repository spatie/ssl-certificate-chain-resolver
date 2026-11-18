<?php

namespace Spatie\CertificateChain\Console;

use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    public function __construct()
    {
        parent::__construct('Ssl certificate chain resolver', '3.1.0');

        $this->addCommand(new ResolveCommand);
    }

    public function getLongVersion(): string
    {
        return parent::getLongVersion().' by <comment>Spatie</comment>';
    }
}
