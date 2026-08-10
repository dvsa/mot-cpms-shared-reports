<?php

return array(
    'logger' => array(
        'filename' => \date('Y-m-d') . '-cpms-reports.log',
        'location'       => '/var/log/dvsa', // log location
        'channel'        => 'cpms-reports',
    ),
);
