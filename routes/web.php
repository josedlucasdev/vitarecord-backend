<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Proxy route for clinical attachments to bypass symlink issues (403 Forbidden)
Route::get('/clinical-assets/{filename}', function ($filename) {
    $path = storage_path('app/public/clinical_attachments/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    $file = file_get_contents($path);
    $type = mime_content_type($path);
    
    return response($file)->header('Content-Type', $type);
});
