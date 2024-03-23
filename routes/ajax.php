<?php

use App\Http\Controllers\AjaxController;
use Illuminate\Support\Facades\Route;


// ajax controller
Route::controller(AjaxController::class)->group(function () {
    Route::post('/notification/markAsRead', 'markAsRead')->name('notification.markAsRead');
});
