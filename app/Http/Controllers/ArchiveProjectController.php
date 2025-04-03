<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArchiveProjectController extends Controller
{
    public function index()
    {
        return view('archiveProjects'); // Ensure this view exists in resources/views
    }
}
