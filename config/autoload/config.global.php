<?php

return [
    'mot_logger' => [
        'loggers' => [
            'cpms-reports' => [
                'channel' => 'cpms-reports',
                'writers' => [
                    [
                        'type' => 'stream',
                        'path' => '/var/log/dvsa/' . \date('Y-m-d') . '-cpms-reports.log',
                        'formatter' => 'json',
                        'level' => 'info',
                        'enabled' => true,
                    ],
                ],
            ],
        ],
    ],
];
