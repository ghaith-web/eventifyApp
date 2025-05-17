<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\TestRedisQueueJob;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-job', function () {
    TestRedisQueueJob::dispatch();
    return 'Job dispatched!';

});
