<?php

return [
    /*
     * Default Pool Name
     */
    'defualt_pool' => 'epay_pool',

    /**
     * Channel Names of Events
     */
    'channels' => [
        'expired' => 'order.trader.expired',
    ],
    'events' => [
        'expired' => 'trader.waiting.expired',
    ],
];
