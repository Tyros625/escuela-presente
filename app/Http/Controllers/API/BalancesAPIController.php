<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateBalancesAPIRequest;
use App\Http\Requests\API\UpdateBalancesAPIRequest;
use App\Http\Resources\BalancesResource;
use App\Models\Tenants\Balances;
use App\Models\Tenants\Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalancesAPIController extends AppBaseController
{
    public function index(Request $request): JsonResponse
    {
        $balances = Balances::when($request->query('type_balance'), function ($query, $type) {
            $query->where('type', $type);
        })->when($request->query('date_start'), function ($query, $date_start) {
            $query->whereDate('created_at', $date_start);
        })->when($request->query('grade'), function ($query, $grade) {
            $query->grade($grade);
        })->when($request->query('group'), function ($query, $group) {
            $query->group($group);
        })->get();

        return $this->sendResponse(BalancesResource::collection($balances), 'Balances retrieved successfully');
    }

    public function store(CreateBalancesAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $student = Student::where('enrollment', $input['student_enrollment'])->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        if ($input['type'] === 'income') {
            $balance = Balances::create([
                'student_id' => $student->id,
                'amount' => $input['amount'],
                'type' => $input['type'],
            ]);
        } else {
            $balance = $this->getBalanceByStudent($student);

            if ($balance < $input['amount']) {
                return $this->sendError('Saldo insuficiente');
            }

            $balances = Balances::where('student_id', $student->id)
                ->where('type', 'expense')
                ->whereDate('created_at', Carbon::today())
                ->get();

            if ($balances->toArray()) {
                return $this->sendError('Ya ha registrado asistencia el día de hoy');
            }

            $balance = Balances::create([
                'student_id' => $student->id,
                'amount' => $input['amount'],
                'type' => $input['type'],
            ]);
        }

        return $this->sendResponse(new BalancesResource($balance), 'Balances saved successfully');
    }

    public function show($enrollment): JsonResponse
    {
        $student = Student::where('enrollment', $enrollment)->first();

        if (empty($student)) {
            return $this->sendError('Student not found');
        }

        $balances = Balances::where('student_id', $student->id)->get();

        if (empty($balances)) {
            return $this->sendError('Balances not found');
        }

        return response()->json([
            'success' => true,
            'data' => $balances,
            'message' => 'Balances retrieved successfully',
        ]);
    }

    public function update($id, UpdateBalancesAPIRequest $request): JsonResponse
    {
        $balances = Balances::find($id);

        if (empty($balances)) {
            return $this->sendError('Balances not found');
        }

        $balances->fill($request->all());
        $balances->save();

        return $this->sendResponse(new BalancesResource($balances), 'Balances updated successfully');
    }

    public function destroy($id): JsonResponse
    {
        $balances = Balances::find($id);

        if (empty($balances)) {
            return $this->sendError('Balances not found');
        }

        $balances->delete();

        return $this->sendSuccess('Balances deleted successfully');
    }

    protected function getBalanceByStudent($student)
    {
        $balances = Balances::where('student_id', $student->id)->get();

        if (empty($balances)) {
            return $this->sendError('Balances not found');
        }

        $balance = 0;
        foreach ($balances as $value) {
            $balance = $value->type === 'income' ? $balance + $value->amount : $balance - $value->amount;
        }

        return $balance;
    }
}
