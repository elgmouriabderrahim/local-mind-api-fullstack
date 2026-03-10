<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Question;
use App\Models\Response;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'questions_count' => Question::count(),
            'responses_count' => Response::count(),
            'favorite_count' => Question::whereHas('favorites')->count(),
        ];

        return response()->json($stats);
    }
}
