# Upgrade guide

Breaking changes per major version, newest first.

## 2.x to 3.0

Guidelines are delivered as an index instead of being inlined. Set `mode` to
`inline` in your published config to keep the old behaviour.

`GuidelinesDiscoverer` and `PackageScanner` moved to the `Discovery`
sub-namespace, and the discoverer now takes a scanner-object as parameter.

## 1.x to 2.0

The package now requires PHP 8.4.
