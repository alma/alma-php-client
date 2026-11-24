<?php

namespace Alma\Client\Tests\Unit\Application\DTO;

use Alma\Client\Application\DTO\AddressDto;
use Alma\Client\Application\DTO\CartDto;
use Alma\Client\Application\DTO\CartItemDto;
use Alma\Client\Application\DTO\PaymentDto;
use Alma\Client\Application\DTO\RequestedProductDto;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PaymentDtoTest extends TestCase
{
    public function testPaymentDto()
    {
        $data = [
            'purchase_amount' => 10000,
            'installments_count' => 4,
            'deferred_months' => 0,
            'deferred_days' => 0,
            'locale' => 'fr_FR',
            'expires_after' => 3600,
            'capture_method' => 'manual',
            'customer_cancel_url' => 'https://example.com/cancel',
            'custom_data' => [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
            'ipn_callback_url' => 'https://example.com/ipn',
            'origin' => PaymentDto::ORIGIN_ONLINE,
            'return_url' => 'https://example.com/return',
            'failure_return_url' => 'https://example.com/failure',
            'billing_address' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@doe.com',
                'phone' => '1234567890',
            ],
            'shipping_address' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@doe.com',
                'phone' => '1234567890',
            ],
            'cart' => [
                'items' => [
                        0 => [
                        'sku' => 'SKU123',
                        'title' => 'My product',
                        'quantity' => 5,
                        'unit_price' => 2500,
                        'line_price' => 12500,
                        'categories' => ['A', 'B'],
                        'url' => 'https://example.com/product',
                        'picture_url' => 'https://example.com/image.jpg',
                        'requires_shipping' => true,
                    ]
                ]
            ]
        ];

        $cartItemDto = (new CartItemDto(
            $data['cart']['items'][0]['quantity'],
            $data['cart']['items'][0]['line_price'],
            $data['cart']['items'][0]['title']
        ))
            ->setSku($data['cart']['items'][0]['sku'])
            ->setUnitPrice($data['cart']['items'][0]['unit_price'])
            ->setCategories($data['cart']['items'][0]['categories'])
            ->setUrl($data['cart']['items'][0]['url'])
            ->setPictureUrl($data['cart']['items'][0]['picture_url'])
            ->setRequiresShipping($data['cart']['items'][0]['requires_shipping']);
        $cartDto = (new CartDto())->addItem($cartItemDto);
        $shippingAddressDto = (new AddressDto())
            ->setFirstName($data['shipping_address']['first_name'])
            ->setLastName($data['shipping_address']['last_name'])
            ->setEmail($data['shipping_address']['email'])
            ->setPhone($data['shipping_address']['phone']);
        $billingAddressDto = (new AddressDto())
            ->setFirstName($data['billing_address']['first_name'])
            ->setLastName($data['billing_address']['last_name'])
            ->setEmail($data['billing_address']['email'])
            ->setPhone($data['billing_address']['phone']);
        $paymentDto = (new PaymentDto($data['purchase_amount']))
            ->setPurchaseAmount($data['purchase_amount'])
            ->setInstallmentsCount($data['installments_count'])
            ->setDeferredMonths($data['deferred_months'])
            ->setDeferredDays($data['deferred_days'])
            ->setLocale($data['locale'])
            ->setExpiresAfter($data['expires_after'])
            ->setCaptureMethod($data['capture_method'])
            ->setCustomerCancelUrl($data['customer_cancel_url'])
            ->setCustomData($data['custom_data'])
            ->setIpnCallbackUrl($data['ipn_callback_url'])
            ->setOrigin($data['origin'])
            ->setReturnUrl($data['return_url'])
            ->setFailureReturnUrl($data['failure_return_url'])
            ->setBillingAddress($billingAddressDto)
            ->setShippingAddress($shippingAddressDto)
            ->setCart($cartDto);

        $this->assertEquals($data, $paymentDto->toArray());
    }

    public function testPaymentDtoWithRequestedProductsAndNoInstallmentsCount(): void
    {
        $paymentDto = (new PaymentDto(30000))
            ->addRequestedProduct((new RequestedProductDto(1))->setDeferredDays(30))
            ->addRequestedProduct(new RequestedProductDto(3))
            ->addRequestedProduct(new RequestedProductDto(4));

        $this->assertSame([
            'installments_count' => null,
            'purchase_amount' => 30000,
            'requested_products' => [
                ['installments_count' => 1, 'deferred_days' => 30],
                ['installments_count' => 3],
                ['installments_count' => 4],
            ],
        ], $paymentDto->toArray());
    }

    public function testPaymentDtoWithRequestedProductsKeepsAnExplicitInstallmentsCount(): void
    {
        $paymentDto = (new PaymentDto(30000))
            ->setInstallmentsCount(3)
            ->addRequestedProduct(new RequestedProductDto(3));

        $this->assertSame(3, $paymentDto->toArray()['installments_count']);
    }

    public function testPaymentDtoWithoutRequestedProductsOmitsTheField(): void
    {
        $this->assertArrayNotHasKey('requested_products', (new PaymentDto(30000))->toArray());
    }

    public function testPaymentDtoException()
    {
        $this->expectException(InvalidArgumentException::class);
        $paymentDto = new PaymentDto(-100);
    }
}