# Alma PHP API Client tests

## Launch tests

We are using [Taskfile](https://taskfile.dev/) to run our development tasks.
[Actively supported PHP versions](https://www.php.net/supported-versions.php) should be used to run our tests.

### Unit tests

```bash
task tests # defaults to latest
task tests PHP_VERSION=8.2
```

### Integration tests

First fill in `ALMA_API_KEY` and `ALMA_API_ROOT` in `phpunit.dist.xml`

```bash
task tests:integration # defaults to latest
task tests:integration PHP_VERSION=8.2
```

### Older PHP versions (5.6 to 8.0)

Copy `phpunit.dist.legacy.xml` to `phpunit.xml`

#### If you are using PHP5.6

You need to change in phpunit.xml the variable {MYVERSION} by "Legacy"

#### If you are using PHP7.0 or PHP7.1

You need to change in phpunit.xml the variable {MYVERSION} by "PHP7_0"

#### If you are using PHP7.2 or PHP7.3 or PHP7.4 or PHP8.0

You need to change in phpunit.xml the variable {MYVERSION} by "PHP7_2"
