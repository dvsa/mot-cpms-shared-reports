# mot-cpms-shared-reports

Laminas Module for CPMS Shared reports

## Installing

The recommended way to install is through [Composer](https://getcomposer.org/).
```
composer require dvsa/mot-cpms-shared-reports
```

## Usage

--- REPORT WORKER ---
This is a hack to run the cron job every 5 second

* * * * * /usr/bin/php /workspace/payment-service/public/index.php report generate
* * * * * sleep 5 && /usr/bin/php /workspace/payment-service/public/index.php report generate
* * * * * sleep 10 && /usr/bin/php /workspace/payment-service/public/index.php report generate
* * * * * sleep 15 && /usr/bin/php /workspace/payment-service/public/index.php report generate
* * * * * sleep 20 && /usr/bin/php /workspace/payment-service/public/index.php report generate
* * * * * sleep 25 && /usr/bin/php /workspace/payment-service/public/index.php report generate
* * * * * sleep 30 && /usr/bin/php /workspace/payment-service/public/index.php report generate
* * * * * sleep 35 && /usr/bin/php /workspace/payment-service/public/index.php report generate
* * * * * sleep 40 && /usr/bin/php /workspace/payment-service/public/index.php report generate
* * * * * sleep 45 && /usr/bin/php /workspace/payment-service/public/index.php report generate
* * * * * sleep 50 && /usr/bin/php /workspace/payment-service/public/index.php report generate
* * * * * sleep 55 && /usr/bin/php /workspace/payment-service/public/index.php report generate

--- REPORT GARBAGE COLLECTOR ---
Cleaning jobs that are (by default) 2 weeks old which run every hour.

0 0 * * * /usr/bin/php /workspace/payment-service/public/index.php report clean

## Contributing

Please refer to our [Contribution Guide](/CONTRIBUTING.md).

