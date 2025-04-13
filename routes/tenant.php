<?php

declare(strict_types=1);

use App\Helper;
use App\Http\Controllers\API\AcademicGroupAPIController;
use App\Http\Controllers\API\AccountConfigurationController;
use App\Http\Controllers\API\AssistAPIController;
use App\Http\Controllers\API\BalancesAPIController;
use App\Http\Controllers\API\DashboardAPIController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\GeneralConfigurationController;
use App\Http\Controllers\API\GradeAPIController;
use App\Http\Controllers\API\IncidentAPIController;
use App\Http\Controllers\API\IncidentReportAPIController;
use App\Http\Controllers\API\PaymentAPIController;
use App\Http\Controllers\API\PaymentPriceAPIController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\SchoolCycleAPIController;
use App\Http\Controllers\API\SectionAPIController;
use App\Http\Controllers\API\SelectListController;
use App\Http\Controllers\API\SpecialtyAPIController;
use App\Http\Controllers\API\StudentAPIController;
use App\Http\Controllers\API\TeacherAPIController;
use App\Http\Controllers\API\UserAPIController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\TenantLoginController;
use App\Jobs\EmailPaymentJob;
use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Grade;
use App\Models\Tenants\Student;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return view('app');
    });

    Route::get('payment/{type}', [RedirectController::class, 'index']);
});

Route::prefix('api')->middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::name('api.')->group(function () {
        Route::get('/', function () {
            return 'This is your API for multi-tenant application. The id of the current tenant is '.tenant('id');
        });

        Route::post('login', [TenantLoginController::class, 'login']);
        Route::post('register', [TenantLoginController::class, 'register']);
        Route::put('change-password', [TenantLoginController::class, 'changePassword']);

        // Payments
        Route::get('payments', [PaymentAPIController::class, 'index']);
        Route::post('payments/save', [PaymentAPIController::class, 'store']);
        Route::post('payments/update/{type}', [PaymentAPIController::class, 'update']);
        Route::delete('payments/{id}', [PaymentAPIController::class, 'destroy']);
        Route::get('payments/export', [PaymentAPIController::class, 'export']);

        // MercadoPago
        Route::post('payments/preference/{type}', [PaymentAPIController::class, 'preferenceMP']);
        Route::post('payments/ipn', [PaymentAPIController::class, 'ipn']);
        Route::post('payments/webhook', [PaymentAPIController::class, 'ipn']);
        Route::post('payments/update', [PaymentAPIController::class, 'updatePayment']);

        // Lists
        Route::get('lists/{type}', [SelectListController::class, 'index']);
        Route::get('lists/{type}/{id}', [SelectListController::class, 'show']);

        // Upload
        Route::post('upload-file', [FileController::class, 'uploadFile']);

        // APP APIS
        Route::get('students/by-curp/{curp}', [StudentAPIController::class, 'curp']);
        Route::get('students/by-id/{id}', [StudentAPIController::class, 'id']);
        Route::get('students/assists/{id}', [AssistAPIController::class, 'id']);
        Route::get('students/incidents/{id}', [IncidentReportAPIController::class, 'id']);

        Route::post('students', [StudentAPIController::class, 'store']);

        Route::middleware('auth:api')->group(function () {
            Route::post('logout', [TenantLoginController::class, 'logout']);
            Route::get('dashboard', [DashboardAPIController::class, 'index']);

            // Users
            Route::apiResource('users', UserAPIController::class);

            // Students
            Route::get('students', [StudentAPIController::class, 'index']);
            Route::get('students/{enrollment}', [StudentAPIController::class, 'show']);
            Route::put('students/{enrollment}', [StudentAPIController::class, 'update']);
            Route::delete('students/{enrollment}', [StudentAPIController::class, 'destroy']);
            Route::post('students/import', [StudentAPIController::class, 'import']);

            // Balances
            Route::get('balances', [BalancesAPIController::class, 'index']);
            Route::post('balances', [BalancesAPIController::class, 'store']);
            Route::get('balances/{enrollment}', [BalancesAPIController::class, 'show']);

            // Assists
            Route::get('assists', [AssistAPIController::class, 'index']);
            Route::post('assists/{enrollment}', [AssistAPIController::class, 'store']);
            Route::get('assists/{enrollment}', [AssistAPIController::class, 'show']);
            Route::post('assists/export/pdf', [AssistAPIController::class, 'exportPDF']);

            // Incidents
            Route::get('incident-reports', [IncidentReportAPIController::class, 'index']);
            Route::post('incident-reports', [IncidentReportAPIController::class, 'store']);
            Route::get('incident-reports/{enrollment}', [IncidentReportAPIController::class, 'show']);
            Route::delete('incident-reports/{enrollment}', [IncidentReportAPIController::class, 'destroy']);

            Route::apiResource('grades', GradeAPIController::class);
            Route::apiResource('sections', SectionAPIController::class);
            Route::apiResource('school-cycles', SchoolCycleAPIController::class);
            Route::apiResource('academic-groups', AcademicGroupAPIController::class);
            Route::apiResource('payment-prices', PaymentPriceAPIController::class);

            // Teachers
            Route::apiResource('teachers', TeacherAPIController::class);
            Route::post('teachers/import', [TeacherAPIController::class, 'import']);

            Route::apiResource('specialties', SpecialtyAPIController::class);

            Route::apiResource('incidents', IncidentAPIController::class);

            // Roles
            Route::apiResource('roles', RoleController::class);
            Route::patch('roles/{role}/permissions', [RoleController::class, 'updatePermissions']);
            Route::get('roles/{role}/users', [RoleController::class, 'userList']);

            // Account Configuration
            Route::get('account-configuration/{user}', [AccountConfigurationController::class, 'index']);
            Route::post('account-configuration/{user}', [AccountConfigurationController::class, 'update']);

            // General Configuration
            Route::get('general-configuration', [GeneralConfigurationController::class, 'index']);
            Route::post('general-configuration', [GeneralConfigurationController::class, 'update']);
        });

        Route::prefix('tests')->group(function () {
            Route::get('/', function () {
                return 'API TESTS';
            });

            Route::get('/email', function () {
                $mailData = [
                    'type' => 'payment.created',
                    'title' => 'Pago Creado en EscuelaPresente.com',
                    'body' => 'This is for testing email using smtp.',
                    'email' => 'jlsc92@gmail.com',
                ];

                $emailJobs = new EmailPaymentJob($mailData);
                dispatch($emailJobs);

                return 'Sending email...';
            });

            Route::get('/check-students', function () {
                $res = Helper::verifySpaceInGrades(1);

                if (empty($res)) {
                    return 'No hay espacio en ningún grupo.';
                }

                return response()->json($res);
            });

            Route::get('/move-students', function () {
                $students = Student::with('academic')->get();

                $students->each(function ($student) {
                    $group = AcademicGroup::where('id', $student->academic_group_id)
                        ->with('grade', 'section', 'schoolCycle')
                        ->first();

                    $grade = Grade::where('order', $group->grade->order + 1)->first();

                    if (empty($grade)) {
                        return;
                    }

                    $group = AcademicGroup::where('grade_id', $grade->id)
                        ->with('grade', 'section', 'schoolCycle')
                        ->first();

                    // $student->academic_group_id = $grade->
                    $student->academic->grade = $grade->description;
                    $student->academic->save();
                });

                return response()->json('Estudiantes Movidos');
            });

            Route::get('/update-grade', function () {
                $students = Student::with('academic')->get();

                $students->each(function ($student) {
                    $group = AcademicGroup::where('id', $student->academic_group_id)
                        ->with('grade', 'section', 'schoolCycle')
                        ->first();

                    $student->academic->grade = $group->grade->description;
                    $student->academic->group = $group->section->description;
                    $student->academic->school_cycle = $group->schoolCycle->description;
                    $student->academic->save();
                });

                return response()->json('Grados Actualizados');
            });

            Route::get('/update-students', function () {
                $students = Student::with('academic')->get();
                $groups = AcademicGroup::with('grade', 'section', 'schoolCycle')->get();

                $students->each(function ($student) use ($groups) {
                    $group = $groups->first(function ($group) use ($student) {
                        $grupo = $group->grade->description.$group->section->description.' '.$group->schoolCycle->description;
                        $estudiante = $student->academic->grade.$student->academic->group.' '.$student->academic->school_cycle;

                        return $grupo === $estudiante;
                    });

                    $student->academic_group_id = $group->id ?? null;
                    $student->save();
                });

                return response()->json('Datos Actualizados');
            });
        });
    });
});
