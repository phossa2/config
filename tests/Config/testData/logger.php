<?php

return [
    'system'    => [
        'channel'   => 'systemLogger',
        'level'     => 'critical'
    ],
    'watchdog'  => [
        'channel'   => 'watchDog',
        'level'     => '${system.logger.level}'
    ]
];
