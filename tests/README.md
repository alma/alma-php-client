Alma PHP API client tests
=====================

You need to change in .docker/Dockerfile ligne 2 your version of PHP

## For PHP 5.6 to 8.0
- copy `phpunit.dist.leg acy.xml` to `phpunit.xml`

#### If you are using PHP5.6

You need to change in phpunit.xml the variable {MYVERSION} by "Legacy"

#### If you are using PHP7.0 or PHP7.1

You need to change in phpunit.xml the variable {MYVERSION} by "PHP7_0"

#### If you are using PHP7.2 or PHP7.3 or PHP7.4 or PHP8.0

You need to change in phpunit.xml the variable {MYVERSION} by "PHP7_2"


## For PHP > 8.0
- copy `phpunit.dist.xml` to `phpunit.xml`

## For all version
- fill in `ALMA_API_KEY` and `ALMA_API_ROOT`



---------------------

to launch unit test :
```
make unit-test
```

to launch integration test :
```
make integration-test
```
