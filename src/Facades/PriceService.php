<?php

namespace Unusualify\Priceable\Facades;

class PriceService extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return \Unusualify\Priceable\PriceService::class;
    }
}
