# Changelog

All notable changes to ssl-certificate-chain-resolver will be documented in this file.

## 3.1.0 - 2026-11-18

- Require PHP 8.4 or higher, Illuminate Collections 12 or 13, and Symfony Console 8.
- Fix command registration for Symfony Console 8.1.
- Migrate the test suite to Pest 5 with offline certificate fixtures.
- Replace Travis CI with GitHub Actions testing PHP 8.4, 8.5, and 8.6 with lowest and latest dependencies.
- Add Pint and PHPStan level 5 checks to CI.

## 2.0.3 - 2020-07-29

- restrict certificate loading to HTTP and HTTPS urls (#34)

## 2.0.2 - 2018-10-01

- fix for Let's Encrypt and Coolblue certificates

## 2.0.1 - 2018-05-09

- add support for symfony 4

## 2.0.0 - 2016-12-15

- Complete rewrite for readability

## 1.0.6 - 2016-09-22

- Upped the required versions for phpseclib, guzzle & symfony console.
- Dropped support for PHP 5.4

## 1.0.5
Fixed: Ignore codecept build files

## 1.0.2
Fixed: Remove all windows line breaks

## 1.0.1
Fixed: Added CertificateFactory

## 1.0.0
Initial release
