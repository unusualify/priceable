![alt text](https://cdn.marshmallow-office.com/media/images/logo/marshmallow.transparent.red.png "marshmallow.")

# Marshmallow Products
Deze package gaat alle logica houden voor producten. Producten zullen in het algemeen gebruikt worden in combinatie met de Cart of Ecommerce package.

### Installatie
```
composer require marshmallow/package-ecommerce
```

Voeg de observer toe aan `AppServiceProvider.php`.
```
public function boot()
{
    ModelObserver::observe();
}
```

php artisan db:seed --class=Marshmallow\\Product\\Database\\Seeds\\VatRatesSeeder

## To do
`php artisan marshmallow:resource Price Priceable`
`php artisan marshmallow:resource VatRate Priceable`
`php artisan marshmallow:resource Currency Priceable`

## Tests
Priceable
is_can_make_use_of_the_price_facade

Currency
it_can_create_a_currency

VAT
it_can_be_created
it_creates_a_slug
it_creates_a_unique_slug
it_calculates_the_multiplier_correctly

Price
it_has_one_currency
it_has_one_vat_rate
it_makes_use_of_default_vatrate_id
it_makes_use_of_default_currency_id
it_calculates_including_price_correctly_from_excluding_amount
it_calculates_excluding_price_correctly_from_excluding_amount
it_calculates_vat_amount_correctly_from_excluding_amount
it_calculates_including_price_correctly_from_including_amount
it_calculates_excluding_price_correctly_from_including_amount
it_calculates_vat_amount_correctly_from_including_amount
it_returns_a_carbon_instance_for_valid_from
it_returns_a_carbon_instance_for_valid_till

## Extra
factory(Marshmallow\Product\Models\Product::class, 10)->create();

## Tests during development
`vendor/bin/phpunit packages/marshmallow/package-priceable`

...