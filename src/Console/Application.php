<?php

namespace Spatie\CertificateChain\Console;

use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    public function __construct()
    {
        parent::__construct('Ssl certificate chain resolver', '2.0.0');

        $command = new ResolveCommand();

        // add() was removed in Symfony 8.1; addCommand() exists since 7.4.
        method_exists($this, 'addCommand') ? $this->addCommand($command) : $this->add($command);
    }

    public function getLongVersion(): string
    {
        return parent::getLongVersion().' by <comment>Spatie</comment>';
    }
}
