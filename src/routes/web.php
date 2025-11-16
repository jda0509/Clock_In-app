<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ApplicationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//staff用
Route::get('/login', [LoginController::class, 'index'])->name('index');
Route::post('/login', [LoginController::class, 'login'])->name('staff.login');
Route::get('/register', [LoginController::class, 'create']);

Route::middleware(['auth:staff', 'verified'])->group(function(){
    Route::get('/attendance', [StaffController::class, 'index'])->name('staff.attendance');
    Route::post('/attendance/start', [StaffController::class, 'startWork'])->name('attendance.start');
    Route::post('/attendance/break/start', [StaffController::class, 'startBreak'])->name('attendance.break.start');
    Route::post('/attendance/break/end', [StaffController::class, 'endBreak'])->name('attendance.break.end');
    Route::post('/attendance/end', [StaffController::class, 'endWork'])->name('attendance.end');
    Route::get('/attendance/list', [AttendanceController::class, 'index'])->name('attendance.list');
    Route::get('/stamp_correction_request/list', [ApplicationController::class, 'index'])->name('application.list');
    Route::get('/stamp_correction_request', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/attendance/detail/{id}', [ApplicationController::class, 'show'])->name('attendances.show');
    Route::post('/stamp_correction_request/detail/{id}', [ApplicationController::class, 'store'])->name('stamp_corrections.store');
});

Route::post('/register', [LoginController::class, 'register'])->name('register');
Route::get('/email/verify', function() {
    return view('auth.verify-email');
})->name('verification.notice');
Route::post('/email/verification-notification', [LoginController::class, 'send'])
    ->middleware(['auth:staff', 'throttle:6,1'])
    ->name('verification.send');
Route::get('/email/verify/{id}/{hash}',function (EmailVerificationRequest $request){
    $request->fulfill();
    return redirect('/attendance');
})->middleware(['auth:staff', 'signed'])->name('verification.verify');


//admin用
Route::get('/admin/login', [LoginController::class, 'adminIndex']);
Route::post('/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login');

Route::prefix('admin')->middleware('auth:admin')->group(function(){
    Route::get('attendance/list/{date?}', [AttendanceController::class,'adminAttendance'])
        ->name('admin.attendance.list');
    Route::get('attendance/list', [AttendanceController::class,'adminAttendance'])
        ->name('admin.attendance');
    Route::get('attendance/{id}', [AttendanceController::class, 'adminShow'])
        ->name('admin.attendance.show');
    Route::get('attendance/staff/{id}', [AttendanceController::class, 'adminStaffMonthly'])
        ->name('admin.staff.monthly');
    Route::post('attendance/{id}', [ApplicationController::class, 'adminUpdate'])
        ->name('admin.attendance.update');
    Route::get('staff/list', [AdminController::class, 'adminStaffList'])
        ->name('admin.staff.list');
    Route::get('/stamp_correction_request/list', [ApplicationController::class, 'index'])
        ->name('admin.application.list');
    Route::get('stamp_correction_request/approve/{id}', [AdminController::class, 'approve'])
        ->name('admin.approve');
    Route::post('stamp_correction_request/approve/{id}', [ApplicationController::class, 'approveSubmit'])
        ->name('admin.approve.submit');
});


Route::get('/dashboard', function() {
    return view('dashboard');
})->middleware(['auth', 'verified']);

Route::middleware('auth:admin')->group(function(){
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

Route::prefix('admin')->middleware(['auth:admin'])->group(function (){
    Route::get('attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::post('attendance/export', [AttendanceController::class, 'exportCsv'])->name('admin.attendance.export');
});


Route::post('/staff/logout', [LoginController::class, 'destroy'])->name('staff.logout');
Route::post('/admin/logout', [LoginController::class, 'adminDestroy'])->name('admin.logout');