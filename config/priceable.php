<?php

return [
    'currency' => env('CURRENCY', env('CASHIER_CURRENCY', 'eur')),
    'currency_locale' => env('CURRENCY_LOCALE', env('CASHIER_CURRENCY_LOCALE', 'nl')),

    'prices_are_including_vat' => true,
    'public_excluding_vat' => env('PRICEABLE_PUBLIC_EXCLUDING_VAT', false),

    /**
     * When we find more then one price on a model when calling
     * the $product->price() method, how should we decide which
     * price to use.
     */
    'on_multiple_prices' => 'lowest', // highest, lowest, eldest, newest

    'models' => [
        'vat' => \Unusualify\Priceable\Models\VatRate::class,
        'price' => \Unusualify\Priceable\Models\Price::class,
        'currency' => \Unusualify\Priceable\Models\Currency::class,
        'price_type' => \Unusualify\Priceable\Models\PriceType::class,
    ],

    'tables' => [
        'vat_rates' => 'vat_rates',
        'currencies' => 'currencies',
        'price_types' => 'price_types',
        'prices' => 'prices',
    ],

    'defaults' => [
        'currencies' => 1,
        'vat_rates' => 1,
        'price_type' => 1,
    ],

    'observers' => [
        'price' => \Unusualify\Priceable\Observers\PriceableObserver::class,
    ],
];
