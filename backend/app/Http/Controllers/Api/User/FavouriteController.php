<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Favourite;
use App\Models\Question;

class FavouriteController extends Controller
{
    public function index()
    {
        $favourites = auth()->user()
            ->favourites()
            ->with(['question.user'])
            ->latest()
            ->get();

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
            $isFavourited = false;
        } else {
            Favourite::create([
                'user_id' => $user->id,
                'question_id' => $question->id
            ]);
            $isFavourited = true;
        }

        return response()->json([
            'message' => 'Favourite toggled successfully.',
            'is_favourited' => $isFavourited,
        ]);
    }
}
