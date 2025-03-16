<?php
return [
    'cash'  => [
        'code'        => 'cash',
        'title'       => 'Cash',
        'description' => 'Cash payment method',
        'class'       => 'Modules\Payments\Payments\Cash',
        'active'      => 1,
        'sort'        => 1,
    ],

    'transfer'  => [
        'code'        => 'transfer',
        'title'       => 'Transfer',
        'description' => 'Bank wire payment method',
        'class'       => 'Modules\Payments\Payments\Transfer',
        'active'      => 1,
        'sort'        => 2,
    ],
];
