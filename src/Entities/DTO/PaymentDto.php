<?php

namespace Alma\API\Entities\DTO;


use InvalidArgumentException;

class PaymentDto {
    private int $purchaseAmount;
    private ?int $installmentsCount = null;
    private ?int $deferredMonths = null;
    private ?int $deferredDays = null;
    private ?string $locale = null;
    private ?int $expiresAfter = null;
    private ?string $captureMethod = null;
    private ?string $customerCancelUrl = null;
    private ?array $customData = null;
    private ?string $ipnCallbackUrl = null;
    private ?string $origin = null;
    private ?string $returnUrl = null;
    private ?string $failureReturnUrl = null;
    private ?array $billingAddress = null;
    private ?array $shippingAddress = null;
    private ?array $cart = null;

    /**
     * PaymentDto constructor.
     * Define mandatory fields for the payment.
     *
     * @param int $purchaseAmount The total amount of the purchase in cents.
     */
    public function __construct(int $purchaseAmount) {
        if ($purchaseAmount < 0) {
            throw new InvalidArgumentException("Purchase amount cannot be negative.");
        }
        $this->purchaseAmount = $purchaseAmount;
    }

    public function setInstallmentsCount(int $installmentsCount): self {
        $this->installmentsCount = $installmentsCount;
        return $this;
    }

    public function setDeferredMonths(int $months): self {
        $this->deferredMonths = $months;
        return $this;
    }

    public function setDeferredDays(int $days): self {
        $this->deferredDays = $days;
        return $this;
    }

    public function setLocale(string $locale): self {
        $this->locale = $locale;
        return $this;
    }

    public function setExpiresAfter(int $minutes): self {
        $this->expiresAfter = $minutes;
        return $this;
    }

    public function setCaptureMethod(string $method): self {
        $this->captureMethod = $method;
        return $this;
    }

    public function setPurchaseAmount(int $amount): self {
        $this->purchaseAmount = $amount;
        return $this;
    }

    public function setCustomerCancelUrl(string $url): self {
        $this->customerCancelUrl = $url;
        return $this;
    }

    public function setCustomData(array $data): self {
        $this->customData = $data;
        return $this;
    }

    public function setIpnCallbackUrl(string $url): self {
        $this->ipnCallbackUrl = $url;
        return $this;
    }

    public function setOrigin(string $origin): self {
        $this->origin = $origin;
        return $this;
    }

    public function setReturnUrl(string $url): self {
        $this->returnUrl = $url;
        return $this;
    }

    public function setFailureReturnUrl(string $url): self {
        $this->failureReturnUrl = $url;
        return $this;
    }

    public function setBillingAddress(AddressDto $address): self {
        $this->billingAddress = $address->toArray();
        return $this;
    }

    public function setShippingAddress(AddressDto $address): self {
        $this->shippingAddress = $address->toArray();
        return $this;
    }

    public function setCart(CartDto $cart): self {
        $this->cart = $cart->toArray();
        return $this;
    }

    public function toArray(): array {
        return array_filter([
            'installments_count'   => $this->installmentsCount,
            'deferred_months'      => $this->deferredMonths,
            'deferred_days'        => $this->deferredDays,
            'locale'               => $this->locale,
            'expires_after'        => $this->expiresAfter,
            'capture_method'       => $this->captureMethod,
            'purchase_amount'      => $this->purchaseAmount,
            'customer_cancel_url'  => $this->customerCancelUrl,
            'custom_data'          => $this->customData,
            'ipn_callback_url'     => $this->ipnCallbackUrl,
            'origin'               => $this->origin,
            'return_url'           => $this->returnUrl,
            'failure_return_url'   => $this->failureReturnUrl,
            'billing_address'      => $this->billingAddress,
            'shipping_address'     => $this->shippingAddress,
            'cart'                 => $this->cart,
        ], function($value) {
            return $value !== null && $value !== '';
        });
    }
}
