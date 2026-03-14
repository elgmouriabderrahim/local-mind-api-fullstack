<?php

namespace App\Http\Controllers\Api\Admin;

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
            'favourites_count' => Question::whereHas('favourites')->count(),
        ];

        return response()->json($stats);
    }
}
