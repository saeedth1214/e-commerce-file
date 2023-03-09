<?php

return [
    'plan' => [
        'afterCallback' => 'https://filymo.ir/front/transaction/verify',
        'callBackUrl' => 'https://filymo.ir/api/frontend/transaction/plan/verify',
    ],
    'order' => [
        'afterCallback' => 'https://filymo.ir/front/transaction/verify',
        'callBackUrl' => 'https://filymo.ir/api/frontend/transaction/order/verify',
    ]
];
