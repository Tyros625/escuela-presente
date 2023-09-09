<?php

namespace App;

use Conekta\Conekta;
use Conekta\Customer;
use Conekta\Order;
use Conekta\ParameterValidationError;
use Conekta\ProcessingError;

class ConektaPayment
{
    public function __construct()
    {
        Conekta::setApiKey(env('CONEKTA_PRIVATE_KEY'));
    }

    public function createOrder($items, $customer)
    {
        $total = 0;
        foreach ($items as $value) {
            $total += $value['unit_price'];
        }

        $valid_order = [
            'line_items' => $items,
            'currency' => 'mxn',
            'metadata' => ['test' => 'extra info'],
            'charges' => [
                [
                    'payment_method' => [
                        'type' => 'oxxo_cash',
                        'expires_at' => strtotime(date('Y-m-d H:i:s')) + '36000',
                    ],
                    'amount' => $total,
                ],
            ],
            'currency' => 'mxn',
            'customer_info' => $customer,
        ];

        try {
            $order = Order::create($valid_order);

            return $order;
        } catch (ProcessingError $e) {
            return $e->getMessage();
        } catch (ParameterValidationError $e) {
            return $e->getMessage();
        }
    }

    public function createOrderWithCheckout($items, $customerId)
    {
        $successUrl = 'https://'.tenant('id').'.'.env('APP_URL_BASE').'/payment/success';
        $failureUrl = 'https://'.tenant('id').'.'.env('APP_URL_BASE').'/payment/failure';

        $validOrder = [
            'line_items' => $items,
            'checkout' => [
                'allowed_payment_methods' => ['cash', 'card', 'bank_transfer'],
                'type' => 'HostedPayment',
                'success_url' => $successUrl,
                'failure_url' => $failureUrl,
                'monthly_installments_enabled' => false,
                'monthly_installments_options' => [3, 6, 9, 12],
                'redirection_time' => 30,
            ],
            'customer_info' => [
                'customer_id' => $customerId,
            ],
            'currency' => 'mxn',
            'metadata' => [
                'test' => 'extra info',
            ],
        ];

        try {
            $order = Order::create($validOrder);

            return $order;
        } catch (ProcessingError $e) {
            return $e->getMessage();
        } catch (ParameterValidationError $e) {
            return $e->getMessage();
        }
    }

    public function createCustomer($customer)
    {
        $customer = Customer::create($customer);

        return $customer;
    }

    public function getOrder($id)
    {
        return Order::find($id);
    }
}
