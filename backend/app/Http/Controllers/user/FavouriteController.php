<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favourite;
use App\Models\Question;

class FavouriteController extends Controller
{
    public function index()
    {
        $favourites = auth()->user()->favourites()->with('question')->get();
        return response()->json($favourites);
    }

    public function toggle(Question $question)
    {
        $user = auth()->user();

        $favourite = Favourite::where('user_id', $user->id)
                            ->where('question_id', $question->id)
                            ->first();

        if ($favourite) {
            $favourite->delete();
        } else {
            Favourite::create([
                'user_id' => $user->id,
                'question_id' => $question->id
            ]);
        }

        return response()->json(['message' => 'Favourite toggled successfully.']);
    }
}
