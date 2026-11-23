<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('taskflow:about', function () {
    $this->info('TaskFlow API is ready.');
})->purpose('Show TaskFlow API information');
