<?php

namespace App\Http\Controllers;


use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{

    public function getFile($fileName): Response
    {

        $file = Storage::disk('public')->get('uploads/' . $fileName);

        return (new Response($file, 200))->header('Content-Type', 'text/csv');

    }
}
