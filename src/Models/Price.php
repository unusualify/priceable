<?php

namespace Unusualify\Priceable\Models;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Marshmallow\HelperFunctions\Facades\Builder as BuilderFacade;
use Unusualify\Priceable\Observers\PriceableObserver;

class Price extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_till' => 'datetime',
    ];

    /**
     * Addind default attributes on __construct level is
     * needed for Nova so it pre-filles the form fields
     * when creating a new resource record.
     * @param array $params [description]
     */
    public function __construct($params = [])
    {
        $default_attributes = $this->defaultAttributes();
        foreach ($default_attributes as $column => $default_value) {
            if (! isset($params[$column])) {
                $params[$column] = $default_value;
            }
        }

        parent::__construct($params);
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::observe(self::getObserver());
    }

    public static function bootObserver(): void
    {
        \Unusualify\Observers\ModelObserver::observe(
            get_class()
        );
    }
    public static function getObserver(): string
    {
        return config('priceable.observers.price', PriceableObserver::class);
    }
    /**
     * This is called when your are saving a model resource.
     * @return [type] [description]
     */
    public function applyDefaultAttributes()
    {
        $default_attributes = $this->defaultAttributes();
        foreach ($default_attributes as $column => $default_value) {
            if (! $this->$column) {
                $this->$column = $default_value;
            }
        }
    }
    /**
     * For a price we need to make sure we always have
     * a VAT rate and a Currency. Selecting them everytime
     * in Nova is a hassle, therefor we set some default
     * that come from the config.
     * @return array Array with default attributes
     */
    public function defaultAttributes(): array
    {
        return [
            'vat_rate_id' => config('priceable.defaults.vat_rates'),
            'currency_id' => config('priceable.defaults.currencies'),
            'price_type_id' => config('priceable.defaults.price_type'),
        ];
    }

    protected function formatAmount($amount, $currency = null)
    {
        return \Unusualify\Priceable\Facades\Price::formatAmount($amount, $currency);
    }

    protected function amount($amount, $currency = null)
    {
        return \Unusualify\Priceable\Facades\Price::amount($amount, $currency);
    }

    /**
     * This will make sure that the submitted amount in Nova
     * is multiplied by 100 so we can store it in cents.
     * @param [type] $amount [description]
     */
    protected function setDisplayPriceAttribute(float $amount)
    {
        $this->attributes['display_price'] = $amount * 100;
    }

    /**
     * This function can be used on the front-end.
     * @return string Formatted price
     */
    public function formatPrice()
    {
        return $this->formatAmount( $this->getPriceExcludingOrIncludingVat() );
    }

    public function price()
    {
        return $this->amount( $this->getPriceExcludingOrIncludingVat() );
    }

    function getPriceExcludingOrIncludingVat(){
        return config('priceable.public_excluding_vat')
            ? $this->price_excluding_vat
            : $this->price_including_vat;
    }

    public function priceAppendingCurrencyString()
    {
        return $this->price() . ' ' . Str::of(config('priceable.currency'))->upper();
    }

    public function pricePrependingCurrencyString()
    {
        return Str::of(config('priceable.currency'))->upper() . ' ' . $this->price();
    }

    public function formatExcludingVat()
    {
        return $this->formatAmount($this->price_excluding_vat);
    }

    public function excludingVat()
    {
        return $this->amount($this->price_excluding_vat);
    }

    public function formatIncludingVat()
    {
        return $this->formatAmount($this->price_including_vat);
    }

    public function includingVat()
    {
        return $this->amount($this->price_including_vat);
    }

    public function formatVat()
    {
        return $this->formatAmount($this->vat_amount);
    }

    public function vat()
    {
        return $this->amount($this->vat_amount);
    }

    /**
     * Scopes
     */
    public function scopeCurrentlyActive(Builder $builder)
    {
        // BuilderFacade::published($builder);
        $valid_from_column = 'valid_from';
        $valid_till_column = 'valid_till';

        $builder->where(function ($builder) use ($valid_from_column, $valid_till_column) {
            /**
             * If both are null, we handle this as active.
             */
            $builder->whereNull($valid_from_column)->whereNull($valid_till_column);
        })->orWhere(function ($builder) use ($valid_from_column, $valid_till_column) {

            /**
             * From is null, and now() is lower than the end date.
             */
            $builder->whereNull($valid_from_column)->where($valid_till_column, '>=', now());
        })->orWhere(function ($builder) use ($valid_from_column, $valid_till_column) {

            /**
             * From is is in the past and the end dat is null
             */
            $builder->where($valid_from_column, '<=', now())->whereNull($valid_till_column);
        })->orWhere(function ($builder) use ($valid_from_column, $valid_till_column) {

            /**
             * From is in the past and till is in the future.
             */
            $builder->where($valid_from_column, '<=', now())->where($valid_till_column, '>=', now());
        });

        return $builder;
    }

    /**
     * Relationships
     */
    public function type()
    {
        return $this->belongsTo(config('priceable.models.price_type'), 'price_type_id');
    }

    public function vatRate()
    {
        return $this->belongsTo(config('priceable.models.vat'));
    }

    public function currency()
    {
        return $this->belongsTo(config('priceable.models.currency'));
    }

    public function priceable()
    {
        return $this->morphTo();
    }

    public function getTable()
    {
        return config('priceable.tables.prices', parent::getTable());
    }
}
