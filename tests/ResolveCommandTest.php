<?php

use Spatie\CertificateChain\Console\Application;
use Spatie\CertificateChain\Exceptions\CouldNotRunCommand;
use Symfony\Component\Console\Tester\CommandTester;

it('has a resolve command to fetch a certificate chain', function () {
    $outputFile = __DIR__.'/temp/certificateChain.crt';
    $command = new CommandTester((new Application)->find('resolve'));

    $command->execute([
        'certificate' => __DIR__.'/fixtures/dv-google/certificate.crt',
        'outputFile' => $outputFile,
    ]);

    $command->assertCommandIsSuccessful();
    expect(str_replace("\r", '', file_get_contents($outputFile)))
        ->toBe(str_replace("\r", '', file_get_contents(__DIR__.'/fixtures/dv-google/certificateChain.crt')));
});

it('only overwrites an existing chain with confirmation', function (string $answer, int $status, bool $overwritten) {
    $outputFile = __DIR__.'/temp/certificateChain.crt';
    file_put_contents($outputFile, 'existing contents');
    $command = new CommandTester((new Application)->find('resolve'));
    $command->setInputs([$answer]);

    $command->execute([
        'certificate' => __DIR__.'/fixtures/dv-google/certificate.crt',
        'outputFile' => $outputFile,
    ]);

    expect($command->getStatusCode())->toBe($status)
        ->and(str_replace("\r", '', file_get_contents($outputFile)))
        ->toBe($overwritten
            ? str_replace("\r", '', file_get_contents(__DIR__.'/fixtures/dv-google/certificateChain.crt'))
            : 'existing contents');
})->with([
    'decline' => ['n', 1, false],
    'default' => ['', 1, false],
    'confirm' => ['y', 0, true],
]);

it('reports a missing input file', function () {
    $command = new CommandTester((new Application)->find('resolve'));

    expect(fn () => $command->execute(['certificate' => __DIR__.'/fixtures/missing.crt']))
        ->toThrow(CouldNotRunCommand::class);
});

it('boots the executable', function () {
    exec(escapeshellarg(PHP_BINARY).' '.escapeshellarg(dirname(__DIR__).'/ssl-certificate-chain-resolver').' resolve --help 2>&1', $output, $status);

    expect($status)->toBe(0)
        ->and(implode("\n", $output))->toContain('Download all intermediate certificates in the trust chain.');
});
