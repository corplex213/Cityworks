<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function LoginPage()
    {
        return view('auth.login'); // Assuming you have a login view at resources/views/auth/login.blade.php
    }
}