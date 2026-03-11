<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\User;

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