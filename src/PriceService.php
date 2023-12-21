<?php

namespace Unusualify\Priceable;

use Money\Money;
use Money\Currency;
use NumberFormatter;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Unusualify\Priceable\Models\VatRate;
use Unusualify\Priceable\Models\Currency as CurrencyModel;

class PriceService
{
    /**
     * @var \Unusualify\Priceable\Models\VatRate
     *
     */
    public $vatRate;

    /**
     * @var \Unusualify\Priceable\Models\Currency
     *
     */
    public $currency;

    /**
     * @var int
     *
     */
    public $display_amount;

    /**
     * @var bool
     *
     */
    public $price_excluding_vat;

    /**
     * @var bool
     *
     */
    public $price_including_vat;

    /**
     * @var double
     *
     */
    public $vat_amount;

    public function make(VatRate $vatRate, CurrencyModel $currency, int $display_amount, bool $display_is_including_vat)
    {
        $this->vatRate = $vatRate;
        $this->currency = $currency;
        $this->display_amount = $display_amount;

        if ($display_is_including_vat) {

            /**
             * The added price is including the VAT. We need to calculate
             * the price without the VAT.
             */
            $price_excluding_vat = round(($display_amount / (100 + $vatRate->rate)) * 100);
            $price_including_vat = $display_amount;
        } else {
            $price_excluding_vat = $display_amount;
            $price_including_vat = round($price_excluding_vat * $vatRate->multiplier());
        }

        $this->price_excluding_vat = $price_excluding_vat;
        $this->price_including_vat = $price_including_vat;

        $this->vat_amount = $this->price_including_vat - $this->price_excluding_vat;

        return $this;
    }

    public function formatAmount($amount, $currency = null, $locale = null)
    {
        $currency = $this->getCurrency($currency);
        $money = new Money($amount, new Currency(strtoupper($currency ?? config('priceable.currency'))));

        $locale = $locale ?? config('priceable.currency_locale');

        $numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies());

        return $moneyFormatter->format($money);
    }

    public function getMoney($amount, Currency $currency = null)
    {
        if (!$currency) {
            $currency = new Currency('eur');
        }

        return new Money($amount, $currency);
    }

    public function getLocale()
    {
        return request()->getUserLocale();
    }

    public function getCurrency(Currency $currency = null)
    {
        if ($currency) {
            return $currency;
        }

        return request()->getUserCurrency()->iso_4217;
    }

    public function amount($amount, $currency = null)
    {
        return round($amount / 100, 2);
    }
}
