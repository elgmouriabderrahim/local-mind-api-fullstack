<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $lat = $request->input('latitude');
        $lng = $request->input('longitude');

        $query = Question::with('user')->withCount('responses');

        if ($lat && $lng) {
            $query->select('*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                    [$lat, $lng, $lat]
                )
                ->orderBy('distance', 'asc');
        } else {
            $query->latest();
        }

        $questions = $query->paginate(10)->withQueryString();

        return response()->json($questions);
    }

    public function create()
    {
        return response()->json(['message' => 'Create question form']);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Question::class);

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string',
            'location_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $question = auth()->user()->questions()->create($validated + [
            'user_id' => auth()->id(),
        ]);

        $question->load('user');

        return response()->json([
            'message' => 'Question created successfully.',
            'data' => $question,
        ], 201);
    }

    public function update(Request $request, Question $question)
    {
        Gate::authorize('update', $question);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:100',
            'content' => 'sometimes|required|string',
            'location_name' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $question->update($validated);
        $question->load('user', 'responses.user');

        return response()->json([
            'message' => 'Question updated successfully.',
            'data' => $question,
        ]);
    }

    public function show(Question $question)
    {
        $question->load('user', 'responses.user');

        return response()->json($question);
    }

    public function destroy(Question $question)
    {
        Gate::authorize('delete', $question);

        $question->delete();

        return response()->json(['message' => 'Question deleted successfully.']);
    }
}