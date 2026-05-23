<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

Schedule::call(function () {
    $files = DB::table('files')->whereNotNull('deleted_at')->get();

    foreach ($files as $file) {
        if (Storage::exists($file->file_path)) {
            Storage::delete($file->file_path);
        }
        DB::table('files')->where('id', $file->id)->delete();
    }
})->daily();

Schedule::command('appointment:send-reminder')->dailyAt('07:00');