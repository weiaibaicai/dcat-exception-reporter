<?php

use Weiaibaicai\DcatExceptionReporter\Http\Controllers\DcatExceptionReporterController;
use Illuminate\Support\Facades\Route;

Route::resource('weiaibaicai/exception-reporters', DcatExceptionReporterController::class);