<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'questions_count' => Question::count(),
        ];

        return response()->json($stats);
    }
}