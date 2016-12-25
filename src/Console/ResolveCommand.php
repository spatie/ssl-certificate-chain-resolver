<?php

namespace Spatie\CertificateChain\Console;

use Exception;
use GuzzleHttp\Client;
use Spatie\CertificateChain\Certificate;
use Spatie\CertificateChain\CertificateChain;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Spatie\CertificateChain\Exceptions\CouldNotRunCommand;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ResolveCommand extends Command
{
    protected $httpClient;

    /**
     * Configure the command options.
     */
    protected function configure()
    {
        $this->setName('resolve')
            ->setDescription('Download all intermediate certificates in the trust chain.')
            ->addArgument('certificate', InputArgument::REQUIRED)
            ->addArgument('outputFile', InputArgument::OPTIONAL);
    }

    public function __construct()
    {
        parent::__construct();

        $this->httpClient = new Client();
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Start resolving trust chain...</info>');

        $certificateFile = $input->getArgument('certificate');

        if (! file_exists($certificateFile)) {
            throw CouldNotRunCommand::inputFileDoesNotExist($certificateFile);
        }

        $outputFile = $input->getArgument('outputFile') ?: 'certificate-including-trust-chain.crt';

        if (file_exists($outputFile)) {
            if (! $this->confirmOverwrite($input, $output, $outputFile)) {
                $output->writeln('<info>Cancelling...</info>');

                return true;
            }
        }

        $certificate = Certificate::loadFromFile($certificateFile);

        $certificateChain = CertificateChain::fetchForCertificate($certificate);

        file_put_contents($outputFile, $certificateChain);

        $output->writeln('<info>Saved trust chain in '.$outputFile.'</info>');
        $output->writeln('<info>All done!</info>');
    }

    protected function guardAgainstInvalidInput(string $certificateFile)
    {
        if (! file_exists($certificateFile)) {
            throw CouldNotRunCommand::inputFileDoesNotExist($certificateFile);
        }
    }

    protected function confirmOverwrite(InputInterface $input, OutputInterface $output, string $outputFile): bool
    {
        $output->writeln('');

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('<comment>Outputfile '.$outputFile.' already exists. Do you want to overwrite it? (y/n) </comment>', false);

        if (! $helper->ask($input, $output, $question)) {
            return false;
        }

        return true;
    }
}
