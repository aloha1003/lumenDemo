<?php

return [
    'purchase' => [
        'App\Models\GiftTransactionOrder',
        'App\Models\BarrageTransactionOrder'
    ],
    'agent' => [
        'App\Models\AgentTransactionList'
    ],
    'receive' => [
        'App\Models\GiftTransactionOrder'
    ],
    'store' => [
        'App\Models\UserTopupOrder'
    ],
    'game' => [
    ],
];
