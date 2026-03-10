<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $usersCount = User::count();
        $questionsCount = Question::count();
        $responsesCount = Response::count();
        $favoritesCount = Favorite::count();

        return view('home', compact('usersCount', 'questionsCount', 'responsesCount', 'favoritesCount'));
    }
}