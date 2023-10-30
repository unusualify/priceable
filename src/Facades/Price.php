<?php

namespace Unusualify\Priceable\Facades;

class Price extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return \Unusualify\Priceable\Price::class;
    }
}
