<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

    public function destroy(Response $response)
    {
        Gate::authorize('delete', $response);

        $response->delete();

        return response()->json(['message' => 'Response deleted successfully.']);
    }
}