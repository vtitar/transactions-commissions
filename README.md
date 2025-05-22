# Overview

Test task for Transaction Commission Calculation.

1. Receive input file with transactions
2. Fetch data from binlist. Added retries and throtling for error 429.
3. Fetch data from exchangeRate api.
4. Added caching.
5. Added unittests and generated code coverage.


# 1. PR 

PR might be easier to review - https://github.com/vtitar/transactions-commissions/pull/1/files
All commits visible.

# 2. Install

```shell
git clone https://github.com/vtitar/transactions-commissions.git
cd transactions-commissions
git fetch
git checkout feature/refactor
```
**Note:** 
1. add .env file
2. add your EXCHANGERATES_API_ACCESSKEY to .env file - example in .env.test
3. if paid plan used - might need to change exchangerates.api.cache-lifetime-seconds param in config/services.yaml
4. continue installation

```shell
composer install
./bin/console cache:clear
```


# 3. Running

Entry point - command \App\Command\CalculateTransactionsCommissionsCommand in src/Command/CalculateTransactionsCommissionsCommand.php

```shell
./bin/console app:transactions:calculate-commissions path/to/your_file.txt
```

in output will be displayed calculated commissions.

**Note:** if error happened - will be displayed error message for that line

# 4. UnitTesting

Created 2 separate scenarios for unit testings:
 - mock httpClient - for better code coverage.
 - mock api(readers) services - for testing app with possibility to change api providers.

Added 4 unit tests:
**Mock HttpClient**
 - success test
 - test with emty line response from binlist
 - throtlled 429 test
 **Mock Services**
 - success test

Currently configured to run only Mock services scenario - as this is requested in task

Tests could be started:

```shell
./vendor/bin/phpunit
```

# 5. Tests code coverage

Generated code coverage(for HttpClientMock). Currently 85%. Could be checked by running in browser coverage/index.html

# 6. Suggested Improvements 

- fix caching issue for unit tests - currently fixed with cache-lifetime 1s.
- add more tests: empty responses, broken lines in responses, broken input file, etc
- improve code coverage. Investigate why classes not checked.
- handle exceptions in real - send notifications.
- add dto validations.
