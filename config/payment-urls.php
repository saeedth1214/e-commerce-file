<?php

return [
    'plan' => [
        'afterCallback' => 'https://filymo.ir/front/transaction/verify',
        'callBackUrl' => 'https://api.filymo.ir/api/frontend/transaction/plan/verify',
    ],
    'order' => [
        'afterCallback' => 'https://filymo.ir/front/transaction/verify',
        'callBackUrl' => 'https://api.filymo.ir/api/frontend/transaction/order/verify',
    ]
];
