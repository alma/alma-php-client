Alma PHP API client tests
=====================

To be able to launch the tests you would require to copy `phpunit.dist.xml` to `phpunit.xml`
and to fill in `ALMA_API_KEY` and `ALMA_API_ROOT`

---------------------

before launching the test, up the container :
```
make up
```

to launch unit test :
```
make test
```

to launch integration test :
```
make integration-test
```

to launch integration and unit test :
```
make test-all
```
