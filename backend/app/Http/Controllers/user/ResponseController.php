<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Response;

class ResponseController extends Controller
{
    public function store(Request $request, Question $question)
    {
        $request->validate(['content' => 'required|string|max:500']);

        $question->responses()->create([
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Response created successfully.']);
    }

    public function index()
    {
        $responses = Response::with('user', 'question')->latest()->paginate(20);
        return response()->json($responses);
    }
}