<?php

/*
|--------------------------------------------------------------------------
| Donation Options
|--------------------------------------------------------------------------
|
| Payment methods and currencies offered on the admin donation form. These
| are recorded manually by staff (bank transfer, cash, mobile banking
| received outside the site) - there is no online payment gateway wired
| up yet, so this is purely a record-keeping + auto-receipt-email list.
|
*/

return [
    'methods' => [
        'bank_transfer' => 'Bank Transfer',
        'mobile_banking' => 'Mobile Banking (bKash/Nagad/Rocket)',
        'cash' => 'Cash',
        'card' => 'Card',
        'other' => 'Other',
    ],

    'currencies' => [
        'BDT' => 'BDT (৳)',
        'USD' => 'USD ($)',
        'EUR' => 'EUR (€)',
        'GBP' => 'GBP (£)',
    ],

    'default_currency' => 'BDT',
];
