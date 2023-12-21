<?php

namespace Unusualify\Priceable\Observers;

use Unusualify\Priceable\Models\Price;

class PriceableObserver
{
    public function saving(Price $price)
    {
        dd(
            $price
        );
        if (config('priceable.prices_are_including_vat')) {

            /**
             * The added price is including the VAT. We need to calculate
             * the price without the VAT.
             */
            $price_excluding_vat = ($price->display_price / (100 + $price->vatRate->rate)) * 100;
        } else {
            $price_excluding_vat = $price->display_price;
        }

        $price->price_excluding_vat = $price_excluding_vat;
        $price->price_including_vat = $price_excluding_vat * $price->vatRate->multiplier();
        $price->vat_amount = $price->price_including_vat - $price->price_excluding_vat;
    }
}
