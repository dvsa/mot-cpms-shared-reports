# mot-cpms-shared-reports

Laminas Module for CPMS Shared reports

## Installing

The recommended way to install is through [Composer](https://getcomposer.org/).
```
composer require dvsa/mot-cpms-shared-reports
```

## Logging Configuration

This module uses **mot-logger v3.3.1** for JSON-formatted logging. The logger is configured to output CPMS reports logs to a stream writer.

### Logger Setup

The logging configuration is defined in `config/autoload/config.global.php` and uses the `mot_logger` configuration structure:

```php
'mot_logger' => [
    'loggers' => [
        'cpms-reports' => [
            'channel' => 'cpms-reports',
            'writers' => [
                [
                    'type' => 'stream',
                    'path' => '/var/log/dvsa/' . date('Y-m-d') . '-cpms-reports.log',
                    'formatter' => 'json',
                    'level' => 'info',
                    'enabled' => true,
                ],
            ],
        ],
    ],
]
```

### Logger Features

- **Channel**: `cpms-reports` - Dedicated channel for CPMS shared report logs
- **Format**: JSON - Structured logging for easier parsing and analysis
- **Level**: INFO - Logs informational messages and above
- **Path**: Daily log rotation via date-based file naming
- **Destination**: `/var/log/dvsa/{YYYY-MM-DD}-cpms-reports.log`

## Usage

### REPORT WORKER

Run the cron job every 5 seconds (this is a workaround for frequent report generation):

```bash
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
```

### REPORT GARBAGE COLLECTOR

Cleaning jobs that are (by default) 2 weeks old which run every hour:

```bash
0 0 * * * /usr/bin/php /workspace/payment-service/public/index.php report clean
```

## Contributing

Please refer to our [Contribution Guide](/CONTRIBUTING.md).

