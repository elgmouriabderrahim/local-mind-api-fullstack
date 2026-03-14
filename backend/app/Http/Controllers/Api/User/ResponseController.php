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
        Gate::authorize('create', Response::class);

        $validated = $request->validate(['content' => 'required|string|max:1000']);

        $response = $question->responses()->create([
            'content' => $validated['content'],
            'user_id' => auth()->id(),
        ]);

        $response->load('user', 'question');

        return response()->json([
            'message' => 'Response created successfully.',
            'data' => $response,
        ], 201);
    }

    public function index()
    {
        $responses = Response::with('user', 'question')->latest()->paginate(20);
        return response()->json($responses);
    }

    public function update(Request $request, Response $response)
    {
        Gate::authorize('update', $response);

        $validated = $request->validate(['content' => 'required|string|max:1000']);

        $response->update($validated);
        $response->load('user', 'question');

        return response()->json([
            'message' => 'Response updated successfully.',
            'data' => $response,
        ]);
    }

    public function destroy(Response $response)
    {
        Gate::authorize('delete', $response);

        $response->delete();

        return response()->json(['message' => 'Response deleted successfully.']);
    }
}