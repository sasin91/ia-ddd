<?php


namespace App\Domains\Billing\Configuration;

use App\Domains\Billing\BillingMethod;
use App\Domains\Billing\Models\Discount;
use DateTimeInterface;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use function config;
use function exchange_rate;
use function is_array;
use function is_string;
use function method_exists;

class BillingMethodOptions
{
    /**
     * The currency code in use
     *
     * @var string
     */
    protected $currency;

    /**
     * An optional billing token used by underlying BillingMethod Gateway
     *
     * @var string|null
     */
    protected $paymentToken;

    /**
     * A reference for tracking the source.
     * eg. an token from the upstream provider.
     *
     * @var string|null
     */
    protected $reference;

    /**
     * The currency rate used for converting to other supported currencies
     *
     * @var float|null
     */
    protected $exchangeRate;

    /**
     * Email address of the customer we withdraw or deposit from/to
     *
     * @var string|null
     */
    protected $customerEmail;

    /**
     * When the model is paid
     *
     * @var DateTimeInterface|string|null
     */
    protected $paidAt = null;

    /**
     * An optional description of the Model
     *
     * @var null|string
     */
    protected $description = null;

    /**
     * Discounts for the amount to be withdrawn
     *
     * @var array
     */
    protected $discounts = [];

    /**
     * Parse the given options into a BillingMethodOptions instance.
     *
     * @param Closure|BillingMethodOptions|array|null $options
     * @return BillingMethodOptions
     */
    public static function parse($options = [])
    {
        if ($options instanceof BillingMethodOptions) {
            return $options;
        }

        return new static($options);
    }

    /**
     * BillingMethodOptions constructor.
     *
     * @param Closure|array $options
     */
    public function __construct($options = [])
    {
        if ($options instanceof Closure) {
            $options($this);
        }

        if (is_array($options)) {
            foreach ($options as $key => $value) {
                $method = 'set'.Str::studly($key);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currency ?? config('currency.default');
    }

    public function getCurrency(): string
    {
        return $this->getCurrencyCode();
    }

    /**
     * @param string $currency
     * @return BillingMethodOptions
     */
    public function setCurrencyCode(?string $currency): BillingMethodOptions
    {
        $this->currency = $currency;
        return $this;
    }

    public function setCurrency(?string $currency): BillingMethodOptions
    {
        return $this->setCurrencyCode($currency);
    }

    /**
     * @return string|null
     */
    public function getPaymentToken(): ?string
    {
        return $this->paymentToken;
    }

    /**
     * @param string|null $paymentToken
     * @return BillingMethodOptions
     */
    public function setPaymentToken(?string $paymentToken): BillingMethodOptions
    {
        $this->paymentToken = $paymentToken;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string|null $reference
     * @return BillingMethodOptions
     */
    public function setReference(?string $reference): BillingMethodOptions
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getExchangeRate(): ?float
    {
        return $this->exchangeRate  ?? exchange_rate(config('currency.default'), $this->getCurrencyCode())->value;
    }

    /**
     * @param float|null $exchangeRate
     * @return BillingMethodOptions
     */
    public function setExchangeRate(?float $exchangeRate): BillingMethodOptions
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    /**
     * @param string|null $customerEmail
     * @return BillingMethodOptions
     */
    public function setCustomerEmail(?string $customerEmail): BillingMethodOptions
    {
        $this->customerEmail = $customerEmail;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getPaidAt():?DateTimeInterface
    {
        return $this->paidAt;
    }

    /**
     * @param DateTimeInterface|string|null $paidAt
     * @return BillingMethodOptions
     */
    public function setPaidAt($paidAt = null): BillingMethodOptions
    {
        if (is_string($paidAt)) {
            $paidAt = Carbon::parse($paidAt);
        }

        $this->paidAt = $paidAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return (string)$this->description;
    }

    /**
     * @param string|null $description
     * @return BillingMethodOptions
     */
    public function setDescription(?string $description): BillingMethodOptions
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the added discounts
     *
     * @return array
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    /**
     * Override the discounts
     *
     * @param array $discounts
     * @return BillingMethodOptions
     */
    public function setDiscounts(array $discounts): BillingMethodOptions
    {
        $this->discounts = $discounts;
        return $this;
    }

    /**
     * Add a discount
     *
     * @param Discount $discount
     * @return BillingMethodOptions
     */
    public function addDiscount(Discount $discount): BillingMethodOptions
    {
        $this->discounts[] = $discount;

        return $this;
    }

    /**
     * Whether any discounts has been added.
     *
     * @return bool
     */
    public function hasDiscounts(): bool
    {
        return !empty($this->discounts);
    }
}
