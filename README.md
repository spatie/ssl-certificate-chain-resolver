# SSL Certificate Chain Resolver
[![Build Status](https://travis-ci.org/freekmurze/ssl-certificate-chain-resolver.svg?branch=master)](https://travis-ci.org/freekmurze/ssl-certificate-chain-resolver)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2912a3ab-51a8-4e07-9bad-fd94a833f989/mini.png)](https://insight.sensiolabs.com/projects/2912a3ab-51a8-4e07-9bad-fd94a833f989) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/freekmurze/ssl-certificate-chain-resolver/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/freekmurze/ssl-certificate-chain-resolver/?branch=master) [![Latest Stable Version](https://poser.pugx.org/spatie/ssl-certificate-chain-resolver/version.png)](https://packagist.org/packages/spatie/ssl-certificate-chain-resolver) [![License](https://poser.pugx.org/spatie/ssl-certificate-chain-resolver/license.png)](https://packagist.org/packages/spatie/ssl-certificate-chain-resolver)

This tool can help you fix the [incomplete certificate chain issue](#background-the-trust-chain), also reported as *Extra download* by [Qualys SSL Server Test](https://www.ssllabs.com/ssltest/).

![Incomplete Chain](images/incomplete-chain.png)

## Installation

This package can be installed using composer by running this command.

```bash
    composer global require spatie/ssl-certificate-chain-resolver
```

## Usage

Let's assume you have an incomplete certificate  called ```cert.crt```. To generate the a file containing the certificate and the entire trust chain, you can use this command:

```bash
ssl-certificate-chain-resolver resolve cert.crt
```

A file containing the certificate and the entire trust chain will be saved as ```certificate-including-trust-chain.crt```

You can also pass the name of the file of the outputfile as the second argument:
```bash
ssl-certificate-chain-resolver resolve cert.crt your-output-file.crt
```

If the outputfile already exists, you will be asked if it's ok to overwrite it.

## Updating

You can update <b>ssl-certificate-chain-resolver</b> to the latest version by running:

```bash
    composer global update spatie/ssl-certificate-chain-resolver
```

## Testing

Functional and unit tests are included with the package.

You can run them yourself with ```vendor/bin/codecept run```


## Credits

- Matthias De Winter
- [Freek Van der Herten](https://murze.be)

This package was inspired by [cert-chain-resolver](https://github.com/zakjan/cert-chain-resolver/) written by [Jan Žák](http://www.zakjan.cz/). Some text from this package is copied from the readme of his repo.


## Background: the trust chain

All operating systems contain a set of default trusted root certificates. But Certificate Authorities usually don't use their root certificate to sign customer certificates. Instead of they use so called intermediate certificates, because they can be rotated more frequently.

A certificate can contain a special Authority Information Access extension (RFC-3280) with URL to issuer's certificate. Most browsers can use the AIA extension to download missing intermediate certificate to complete the certificate chain. This is the exact meaning of the Extra download message. But some clients, mostly mobile browsers, don't support this extension, so they report such certificate as untrusted.

This results in 'untrusted'-warnings like this, since the browser thinks you are on an insecure connection.

![Untrusted Warning](images/untrusted.png)

A server should always send a complete chain, which means concatenated all certificates from the certificate to the trusted root certificate (exclusive, in this order), to prevent such issues.  So when installing a SSL certificate on a server you should install all intermediate certificates as wel. You should be able to fetch intermediate certificates from the issuer and concat them together by yourself.

This tool helps you automatize that boring task by looping over certificate's AIA extension field.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.


