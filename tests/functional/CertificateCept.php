<?php

$command = "php ./ssl-certificate-chain-resolver resolve tests/_data/google.crt tests/_output/trustChain.crt";

$I = new FunctionalTester($scenario);
$I->wantTo('compare input and output.');

$I->runShellCommand($command);

$I->seeFileFound('trustChain.crt', 'tests/_output');

$expected = file_get_contents("tests/_data/expectedTrustChain.crt");
$output = file_get_contents("tests/_output/trustChain.crt");

$expected = str_replace("\r", "", $expected);
$output = str_replace("\r", "", $output);


PHPUnit_Framework_Assert::assertEquals($expected, $output);
