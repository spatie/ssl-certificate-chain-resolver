<?php

namespace Spatie\CertificateChain;

use Exception;
use GuzzleHttp\Client;
use Spatie\CertificateChain\Certificate;
use Spatie\CertificateChain\Exceptions\CouldNotRunCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
        $outputFile = $input->getArgument('outputFile') ?: 'certificate-including-trust-chain.crt';

        $this->guardAgainstInvalidInput($certificateFile);

        if (file_exists($outputFile)) {
            $confirmation = $this->confirmOverwrite($input, $output, $outputFile);

            if (! $confirmation) {
                $output->writeln('<info>Cancelling...</info>');

                return true;
            }
        }

        $certificateChain = $this->getCertificateChain($certificateFile, $output);

        file_put_contents($outputFile, $certificateChain);

        $output->writeln('<info>Saved trust chain in '.$outputFile.'</info>');
        $output->writeln('<info>All done!</info>');
    }

    /**
     * Get a string with the contents of the given certificatefile and it's entire trust chain.
     *
     * @param string          $certificateFile
     * @param OutputInterface $output
     *
     * @return string
     *
     * @throws Exception
     */
    protected function getCertificateChain($certificateFile, OutputInterface $output)
    {
        $output->writeln('');
        $certificateCounter = 1;

        $certificate = new Certificate(file_get_contents($certificateFile));
        $certificateChain = $certificate->getContents();

        while ($certificate->hasParentInTrustChain()) {
            $output->writeln('<comment>Adding certificate '.$certificateCounter.'</comment>');
            $output->writeln('<comment>downloading certificate from URL: '.$certificate->getParentCertificateURL().'</comment>');

            $httpResponse = $this->httpClient->get($certificate->getParentCertificateURL());

            if ($httpResponse->getStatusCode() != 200) {
                throw new Exception('could not download certifcate at '.$certificate->getParentCertificateURL());
            }

            $certificate = new Certificate((string) $httpResponse->getBody());
            $certificateChain .= $certificate->getContents();

            $output->writeln('<comment>added downloaded certificate to trustchain, issuer DN: '.$certificate->getIssuerDN().'</comment>');
            $output->writeln('');

            $certificateCounter++;
        }

        return $certificateChain;
    }

    /**
     * Validate the inputfile.
     *
     * @param $certificateFile
     *
     * @throws Exception
     */
    protected function guardAgainstInvalidInput($certificateFile)
    {
        if (! file_exists($certificateFile)) {
            throw CouldNotRunCommand::inputFileDoesNotExist($certificateFile);
        }
    }

    /**
     * Check if outputfile already exists,
     * if so, ask the user confirmation to overwrite.
     *
     * @param $input
     * @param $output
     * @param $outputFile
     *
     * @return bool
     */
    protected function confirmOverwrite($input, $output, $outputFile)
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
