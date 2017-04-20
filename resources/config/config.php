<?php

use Anomaly\Streams\Platform\Stream\Contract\StreamInterface;

return [
    'add_row' => [
        'type' => 'anomaly.field_type.text',
    ],
    'min'     => [
        'type'   => 'anomaly.field_type.integer',
        'config' => [
            'min' => 1,
        ],
    ],
    'max'     => [
        'type'   => 'anomaly.field_type.integer',
        'config' => [
            'min' => 1,
        ],
    ],
];
