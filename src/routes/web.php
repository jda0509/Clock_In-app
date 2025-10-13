<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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
Route::get('/login', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'login'])->name('staff.login');
Route::get('/register', [LoginController::class, 'create']);
Route::post('/register', [LoginController::class, 'register'])->name('staff.register');

Route::middleware(['auth:staff', 'verified'])->group(function(){
    Route::get('/attendance', [StaffController::class, 'index'])->name('staff.attendance');
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
Route::get('/admin/attendance/list', [LoginController::class, 'adminAttendance'])->name('admin.attendance');



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

