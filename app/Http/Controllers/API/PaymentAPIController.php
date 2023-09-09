<?php

namespace App\Http\Controllers\API;

use App\Exports\PaymentsExport;
use App\Helper;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateStudentAPIRequest;
use App\Http\Requests\API\UpdateStudentAPIRequest;
use App\Http\Resources\PaymentResource;
use App\Jobs\EmailPaymentJob;
use App\MercadoPagoPayment;
use App\Models\Tenants\GeneralConfiguration;
use App\Models\Tenants\Payment;
use App\Models\Tenants\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

class PaymentAPIController extends AppBaseController
{
    public function index(Request $request)
    {
        $payments = Payment::when($request->query('status'), function ($query, $status) {
            if ($status === 'paid') {
                $query->paid();
            } else {
                $query->pending();
            }
        })->latest()->get();

        return $this->sendResponse(PaymentResource::collection($payments), 'Grades retrieved successfully');
    }

    public function preferenceMP(Request $request, $type)
    {
        $config = GeneralConfiguration::first();
        $item = ['quantity' => 1];

        switch ($type) {
            case 'NUEVO INGRESO':
                $unit_price = $config->prices['credentials'];
                $item['title'] = 'Pago Credencial';
                $item['unit_price'] = $unit_price;
                break;
            case 'REINSCRIPCIÓN':
                $unit_price = $config->prices['reentry'];
                $item['title'] = 'Reinscripción';
                $item['unit_price'] = $unit_price;
                break;
            case 'REPOSICIÓN DE CREDENCIAL':
                $unit_price = $config->prices['replacement'];
                $item['title'] = 'Reposición Credencial';
                $item['unit_price'] = $unit_price;
                break;
        }

        $order = new MercadoPagoPayment();
        $id = $order->createPreference($item);

        $payment = new Payment();
        $payment->preference_id = $id;
        $payment->status = 'pending';
        $payment->student_id = $request->id;
        $payment->payment_type = $type;
        $payment->amount = $unit_price;
        $payment->save();

        return $id;
    }

    public function store(CreateStudentAPIRequest $request)
    {
        $space = Helper::verifySpaceInGrades($request->academic_group_id);

        if (empty($space)) {
            return response()->json([
                'message' => 'No hay espacio en ningún grupo de este grado.',
            ], 500);
        }

        $customer = [
            'name' => $request->name,
            'last_name' => $request->last_name_father.', '.$request->last_name_mother,
            'phone' => (string) $request->home_phone,
            'email' => $request->email,
        ];

        $order = new MercadoPagoPayment();
        $customer = $order->createCustomer($customer);

        $mailData = [
            'type' => 'payment.created',
            'title' => 'Pago Creado en EscuelaPresente.com',
            'body' => 'This is for testing email using SMTP.',
            'email' => $request->email,
        ];

        DB::beginTransaction();
        try {
            if (isset($customer)) {
                //Create and Register Student
                $student = Student::create($request->all());
                $student->academic()->create($request->academic);
                $student->relative()->create($request->relatives);
                $student->socioeconomic()->create($request->socioeconomics);
                $student->health()->create($request->healths);

                $student->mercado_pago_id = $customer->id;
                $student->academic_group_id = $space->id;
                $student->update();

                $config = GeneralConfiguration::first();
                $config->last_enrollment = intval($config->last_enrollment) + 1;
                $config->update();

                $emailJobs = new EmailPaymentJob($mailData);
                dispatch($emailJobs);

                DB::commit();

                return response()->json($student, 200);
            }
        } catch (\Exception $e) {
            DB::rollback();

            return $e->getMessage();
        }
    }

    public function update(UpdateStudentAPIRequest $request)
    {
        $student = Student::withoutGlobalScopes()->find($request->id);

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        $student->fill($request->all());
        $student->save();

        $student->academic->update($request->academic);
        $student->relative->update($request->relatives);
        $student->socioeconomic->update($request->socioeconomics);
        $student->health->update($request->healths);

        $space = Helper::verifySpaceInGrades($request->academic_group_id);

        if (empty($space)) {
            return response()->json(['message' => 'No hay espacio en ningún grupo de este grado.'], 500);
        }

        $customer = [
            'name' => $request->name,
            'last_name' => $request->last_name_father.', '.$request->last_name_mother,
            'phone' => (string) $request->home_phone,
            'email' => $request->email,
        ];

        if (! isset($student->mercado_pago_id)) {
            $order = new MercadoPagoPayment();
            $customer = $order->createCustomer($customer);

            $student->mercado_pago_id = $customer->id;
            $student->academic_group_id = $space->id;
            $student->update();
        }

        $mailData = [
            'type' => 'payment.updated',
            'title' => 'Pago Actualizado en EscuelaPresente.com',
            'body' => 'This is for testing email using SMTP.',
            'email' => $request->email,
        ];

        $emailJobs = new EmailPaymentJob($mailData);
        dispatch($emailJobs);

        return response()->json($student, 200);
    }

    public function updatePayment(Request $request)
    {
        DB::beginTransaction();
        try {
            $payment = Payment::where('preference_id', $request->preference_id)->first();
            $payment->payment_id = $request->payment_id;
            $payment->merchant_order_id = $request->merchant_order_id;
            $payment->payment_method = $request->payment_type;
            $payment->status = $request->status;
            $payment->update();

            $student = Student::withoutGlobalScopes()->find($payment->student_id);
            $student->active = true;
            $student->update();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return $e->getMessage();
        }

        return response()->json(['message' => 'Actualizado Correctamente'], 200);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $payment = Payment::find($id);

            if (empty($payment)) {
                return $this->sendError('Payment not found');
            }

            $payment->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();

            return $e->getMessage();
        }

        return $this->sendSuccess('Balances deleted successfully');
    }

    public function ipn(Request $request)
    {
        $params = $request->query->all();
        $order = @file_get_contents('php://input');
        $order = json_decode($order, true);
        $token = env('MERCADO_PAGO_ACCESS_TOKEN');

        if (! empty($params)) {
            if (array_key_exists('topic', $params)) {
                if ($params['topic'] === 'merchant_order') {
                    $response = Http::withToken($token)->get("https://api.mercadolibre.com/merchant_orders/{$params['id']}");
                    $response = json_decode($response->body());

                    $payment = Payment::where('preference_id', $response->preference_id)->first();

                    if (empty($payment)) {
                        return $this->sendError('Payment not found');
                    }

                    $payment->status = $response->status;
                    $payment->amount = $response->total_amount;
                    $payment->merchant_order_id = $params['id'];
                    $payment->update();

                    return response()->json([
                        'success' => true,
                        'data' => $payment,
                        'message' => 'Orden Actualizada',
                    ], 200);
                }

                if ($params['topic'] === 'payment') {
                    $response = Http::withToken($token)->get("https://api.mercadolibre.com/collections/notifications/{$params['id']}");
                    $response = json_decode($response->body());

                    $payment = Payment::where('merchant_order_id', $response->collection->merchant_order_id)->first();

                    if (empty($payment)) {
                        return $this->sendError('Payment not found');
                    }

                    $payment->status = $response->collection->status;
                    $payment->payment_method = $response->collection->payment_type;
                    $payment->amount = $response->collection->total_paid_amount;
                    $payment->merchant_order_id = $response->collection->merchant_order_id;
                    $payment->update();

                    return response()->json([
                        'success' => true,
                        'data' => $payment,
                        'message' => 'Orden Actualizada',
                    ], 200);
                }
            }

            if (array_key_exists('type', $params)) {
                if ($params['type'] === 'payment') {
                    $response = Http::withToken($token)->get("https://api.mercadopago.com/v1/payments/{$request->data_id}");
                    $response = json_decode($response->body());

                    $payment = Payment::where('merchant_order_id', $response->order->id)->first();

                    if (empty($payment)) {
                        return $this->sendError('Payment not found');
                    }

                    $payment->payment_method = $response->payment_method->type;
                    $payment->status = $response->status;
                    $payment->payment_id = $request->data_id;
                    $payment->update();

                    return response()->json([
                        'success' => true,
                        'data' => $payment,
                        'message' => 'Orden Actualizada',
                    ], 200);
                }
            }

            if ($order['action'] === 'payment.updated') {
                $orderId = $order['data']['id'];
                $payment = Payment::where('preference_id', $orderId)->first();

                if (empty($payment)) {
                    return $this->sendError('Payment not found');
                }

                $payment->status = 'paid';
                $payment->update();

                $student = Student::where('id', $payment->student_id)->first();

                if (empty($student)) {
                    return $this->sendError('Student not found');
                }

                $student->active = true;
                $student->update();

                return response()->json([
                    'success' => true,
                    'message' => 'Orden actualizada.',
                ], 200);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'No hay nada que actualizar',
        ], 200);
    }

    public function export()
    {
        $payments = Payment::with(
            'student',
            'student.academicGroup.grade',
            'student.academicGroup.section'
        )->get();
        $dateNow = Carbon::now()->setTimezone('America/Mexico_City')->toDateTimeString();

        return Excel::download(new PaymentsExport($payments), "payments-{$dateNow}.xlsx");
    }
}
