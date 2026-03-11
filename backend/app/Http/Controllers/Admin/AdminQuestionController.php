<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Support\Facades\Gate;

class AdminQuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('user')->latest()->paginate(15);
        return response()->json($questions);
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
