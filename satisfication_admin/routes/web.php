<?php

use App\Http\Controllers\BackgroundController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\RabbitMqController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SequenceController;
use App\Http\Controllers\ServiceDeskController;
use App\Http\Controllers\ServiceLocationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientDeviceController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\StatController;
use App\Http\Controllers\WorkingTimeController;
use App\Http\Controllers\QrodeController;

Route::get('/', [ServiceLocationController::class, 'index']);

//Client
Route::get('/client/', [ClientDeviceController::class, 'index'])->name('client');
Route::post('/client/saveSettings', [ClientDeviceController::class, 'saveSettings']);

//Location
Route::get('/location', [ServiceLocationController::class, 'index']);
Route::get('/location/list', [ServiceLocationController::class, 'getList']);
Route::post('/location/saveData', [ServiceLocationController::class, 'saveData']);
Route::delete('/location/deleteData', [ServiceLocationController::class, 'deleteData']);


//Counter
Route::get('/counter/{id}/{name}', [ServiceDeskController::class, 'index']);
Route::get('/getcounter/{id}', [ServiceDeskController::class, 'getList']);
Route::get('/counter/getListForClient', [ServiceDeskController::class, 'getListForClient']);
Route::post('/counter/unlinkDevice', [ServiceDeskController::class, 'unlinkDevice']);
Route::delete('/counter/deleteCounter', [ServiceDeskController::class, 'deleteData']);
Route::post('/counter/saveData', [ServiceDeskController::class, 'saveData']);
Route::get('/counter/listbylocationid', [ServiceDeskController::class, 'getCounterByLocationId']);


Route::post('/upload-photo', [PhotoController::class, 'uploadPhoto']);
Route::get('/satisfication/', [FeedbackController::class, 'index']);

Route::get('/satisfication/list', [FeedbackController::class, 'getList']);
Route::post('/satisfication/updateData', [FeedbackController::class, 'updateData']);
Route::get('/feedbackanswer/list', [FeedbackController::class, 'getList']);
Route::get('/feedbackanswer/listClient', [FeedbackController::class, 'getListForClient']);
Route::post('/feedbackanswer/saveData', [FeedbackController::class, 'saveData']);
Route::post('/feedbackanswer/reorderData', [FeedbackController::class, 'reorderData']);
Route::delete('/feedbackanswer/deleteData', [FeedbackController::class, 'deleteData']);
Route::post('/feedbackanswer/sendFeedback', [FeedbackController::class, 'saveFeedback']);

// Image
Route::get('/image/{imagename}', [BackgroundController::class, 'getImage']);
Route::post('/image/upload', [BackgroundController::class, 'upload']);

//Login
Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle'])->name('callbackGoogle');
Route::get('getUsername', [GoogleAuthController::class, 'getUsername']);
Route::post('logout', [GoogleAuthController::class, 'logout'])->name('logout'); // Logout route

//Employee Login
Route::get('employee/register', [GoogleAuthController::class, 'register']);

//Employee
Route::get('employee/', [EmployeeController::class, 'index']);
Route::get('employee/list', [EmployeeController::class, 'getList']);


//Sequence
Route::get('/sequence/getSquence', [SequenceController::class, 'getSequence']);
Route::post('/sequence/updateSequence', [SequenceController::class, 'updateSequence']);

//Client
Route::get('/client/checkStatus', [ClientDeviceController::class, 'checkServerStatus']);
Route::post('/client/registerDevice', [ClientDeviceController::class, 'registerDevice']);
Route::get('/client/checkUpdate', [ClientDeviceController::class, 'checkClientSettings']);

//Stat
Route::get('/stats/', [StatController::class, 'index']);
Route::get('/statsbylocation/{id}', [StatController::class, 'statsByLocation']);
Route::get('/stats/statsbylocation', [StatController::class, 'getStatsByLocation']);
Route::get('/stats/overall', [StatController::class, 'getOverallStat']);
Route::get('/stats/today', [StatController::class, 'getTodayStat']);
Route::get('/stats/topFeedback', [StatController::class, 'getTopFeedback']);
Route::get('/stats/printstats', [StatController::class, 'goToPrintStats']);
Route::get('/stats/printbypicklocation', [StatController::class, 'printStatsByPickLocation']);
Route::get('/stats/testexcel', [StatController::class, 'printStatsByPickLocation']);
Route::get('/stats/printselected/', [StatController::class, 'printSelected']);
Route::get('/stats/getstatbycounter/', [StatController::class, 'getFeedbackByCounterId']);


//WorkingTime
// Route::get('/checkin/{id}', [WorkingTimeController::class, 'checkIn']);
Route::get('/checkin/{counter}', [WorkingTimeController::class, 'checkin']);
Route::get('/workingtime/checkinNow', [WorkingTimeController::class, 'isCheckInAvail']);
Route::get('/workingtime/', [WorkingTimeController::class, 'index']);
Route::get('/workingtime/list', [WorkingTimeController::class, 'getList']);
Route::delete('/workingtime/deleteData', [WorkingTimeController::class, 'deleteData']);
Route::post('/workingtime/saveData', [WorkingTimeController::class, 'saveData']);
Route::post('/workingtime/saveCheckIn', [WorkingTimeController::class, 'saveCheckIn']);
Route::get('/workingtime/TodayCheckinList/', [WorkingTimeController::class, 'getTodayCheckInView']);
Route::get('/workingtime/getTodayCheckin/', [WorkingTimeController::class, 'getTodayCheckIn']);
Route::get('/workingtime/historyworkingtime/', [WorkingTimeController::class, 'getWorkingTimeHistoryView']);
Route::get('/workingtime/getalltodayworkingtime/', [WorkingTimeController::class, 'getTodayWorkingTime']);
Route::post('/workingtime/saveCheckIn/', [WorkingTimeController::class, 'saveCheckIn']);
Route::get('/workingtime/settings/', [WorkingTimeController::class, 'settings']);
Route::get('/workingtime/getadvancetime/', [WorkingTimeController::class, 'getAdvancetime']);
Route::post('/workingtime/upadateadvanceduration/', [WorkingTimeController::class, 'updateAdvanceDuration']);

//Role
Route::get('/role/', [RoleController::class, 'index']);
Route::get('/role/list', [RoleController::class, 'getList']);
Route::get('/role/all', [RoleController::class, 'getAllRole']);
Route::post('/role/update', [RoleController::class, 'updateRole']);

//QrCode
Route::get('/genqr', [QrodeController::class, 'generate']);
Route::get('/download-qr-code-pdf', [QrodeController::class, 'downloadQRCodePDF'])->name('download.qrcode.pdf');

//RabbitMq
Route::POST('/send-message', function () {
    $controller = new \App\Http\Controllers\RabbitMqController();
    $controller->sendFanoutMessage('Hello, fanout exchange!');
});
Route::GET('/client/sendupdate', [RabbitMqController::class, 'sendCheckUpdatetoClient']);

// เอาออกเมื่อ Dev เสร็จ
Route::get('/token', function () {
    return csrf_token();
});










