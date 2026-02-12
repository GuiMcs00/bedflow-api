<?php

use App\Http\Controllers\Api\BedController;
use App\Http\Controllers\Api\BedTransferController;
use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/health-check-api', function () {
    return response('checked!', 200);
});

Route::prefix('beds')->group(function () {
    Route::get('/', [BedController::class, 'index']);
    Route::get('/{bed}', [BedController::class, 'show'])
        ->whereNumber('bed');
    Route::post('/{bed}/occupy', [BedController::class, 'occupy'])
        ->whereNumber('bed');
    Route::post('/{bed}/release', [BedController::class, 'release'])
        ->whereNumber('bed');

    Route::post('/transfer', [BedTransferController::class, 'transfer']);
});

Route::get('/patients/{cpf}/bed', [PatientController::class, 'bedByCpf']);