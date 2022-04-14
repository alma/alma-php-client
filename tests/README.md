Alma PHP API client tests
=====================

To be able to launch the tests you would require to copy `phpunit.dist.xml` to `phpunit.xml`
and to fill in `ALMA_API_KEY` and `ALMA_API_ROOT`

---------------------

to launch unit test :
```
make up # only to launch once
make test
```

to launch integration test :
```
make up # only to launch once
make integration-test
```

to launch integration and unit test :
```
make up # only to launch once
make test-all
```
