<?php

namespace App;

use Illuminate\Support\Facades\Http;
use MercadoPago\Customer;
use MercadoPago\Item;
use MercadoPago\MerchantOrder;
use MercadoPago\Preference;
use MercadoPago\SDK;

class MercadoPagoPayment
{
    public function __construct()
    {
        SDK::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));
    }

    public function createPreference($data)
    {
        $successUrl = 'https://'.tenant('id').'.'.env('APP_URL_BASE').'/payment/success';
        $failureUrl = 'https://'.tenant('id').'.'.env('APP_URL_BASE').'/payment/failure';
        $pendingUrl = 'https://'.tenant('id').'.'.env('APP_URL_BASE').'/payment/pending';
        $notificationUrl = 'https://'.tenant('id').'.'.env('APP_URL_BASE').'/api/payments/ipn';

        // Crea un objeto de preferencia
        $preference = new Preference();

        // Crea un ítem en la preferencia
        $item = new Item();
        $item->title = $data['title'];
        $item->quantity = $data['quantity'];
        $item->unit_price = $data['unit_price'];

        $preference->items = [$item];
        $preference->back_urls = [
            'success' => $successUrl,
            'failure' => $failureUrl,
            'pending' => $pendingUrl,
        ];
        $preference->auto_return = 'approved';
        $preference->notification_url = $notificationUrl;
        $preference->save();

        return $preference->id;
    }

    public function createCustomer($data)
    {
        $customer = new Customer();
        $customer->first_name = $data['name'];
        $customer->last_name = $data['last_name'];
        $customer->email = $data['email'];
        $customer->phone = [
            'area_code' => '',
            'number' => $data['email'],
        ];
        $customer->save();

        return $customer;
    }

    public function searchCustomer($id)
    {
        $filters = [
            'id' => $id,
        ];

        $customers = Customer::search($filters);

        return $customers;
    }

    public function getOrder($id)
    {
        $order = MerchantOrder::get($id);

        return $order;
    }

    public function getPayment($id)
    {
        $token = env('MERCADO_PAGO_ACCESS_TOKEN');
        $response = Http::withToken($token)->get("https://api.mercadopago.com/v1/payments/{$id}");

        return response()->json(json_decode($response->body()));
    }

    public function getPreference($id)
    {
        $preference = Preference::get($id);

        return $preference;
    }
}
