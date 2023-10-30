<?php

namespace Tests\Unit;

use Tests\TestCase;
use Unusualify\Priceable\Facades\Price;
use Unusualify\Priceable\Models\Currency;

class CurrencyTest extends TestCase
{
    public function testItCanBeCreated()
    {
    	$currency = Currency::create([
    		'name' => 'EURO'
    	]);
    	$this->assertInstanceOf(Currency::class, $currency);
    }
}
