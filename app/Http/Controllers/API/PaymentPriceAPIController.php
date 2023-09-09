<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreatePaymentPriceAPIRequest;
use App\Http\Requests\API\UpdatePaymentPriceAPIRequest;
use App\Http\Resources\PaymentPriceResource;
use App\Models\Tenants\PaymentPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class PaymentPriceAPIController
 */
class PaymentPriceAPIController extends AppBaseController
{
    /**
     * Display a listing of the PaymentPrices.
     * GET|HEAD /payment-prices
     */
    public function index(Request $request): JsonResponse
    {
        $query = PaymentPrice::query();

        if ($request->get('skip')) {
            $query->skip($request->get('skip'));
        }
        if ($request->get('limit')) {
            $query->limit($request->get('limit'));
        }

        $paymentPrices = $query->get();

        return $this->sendResponse(PaymentPriceResource::collection($paymentPrices), 'Payment Prices retrieved successfully');
    }

    /**
     * Store a newly created PaymentPrice in storage.
     * POST /payment-prices
     */
    public function store(CreatePaymentPriceAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var PaymentPrice $paymentPrice */
        $paymentPrice = PaymentPrice::create($input);

        return $this->sendResponse(new PaymentPriceResource($paymentPrice), 'Payment Price saved successfully');
    }

    /**
     * Display the specified PaymentPrice.
     * GET|HEAD /payment-prices/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var PaymentPrice $paymentPrice */
        $paymentPrice = PaymentPrice::find($id);

        if (empty($paymentPrice)) {
            return $this->sendError('Payment Price not found');
        }

        return $this->sendResponse(new PaymentPriceResource($paymentPrice), 'Payment Price retrieved successfully');
    }

    /**
     * Update the specified PaymentPrice in storage.
     * PUT/PATCH /payment-prices/{id}
     */
    public function update($id, UpdatePaymentPriceAPIRequest $request): JsonResponse
    {
        /** @var PaymentPrice $paymentPrice */
        $paymentPrice = PaymentPrice::find($id);

        if (empty($paymentPrice)) {
            return $this->sendError('Payment Price not found');
        }

        $paymentPrice->fill($request->all());
        $paymentPrice->save();

        return $this->sendResponse(new PaymentPriceResource($paymentPrice), 'PaymentPrice updated successfully');
    }

    /**
     * Remove the specified PaymentPrice from storage.
     * DELETE /payment-prices/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var PaymentPrice $paymentPrice */
        $paymentPrice = PaymentPrice::find($id);

        if (empty($paymentPrice)) {
            return $this->sendError('Payment Price not found');
        }

        $paymentPrice->delete();

        return $this->sendSuccess('Payment Price deleted successfully');
    }
}
