<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $data = $request->validate([
            'review' => 'required|string',
            'rating' => 'required|numeric|in:1,2,3,4,5'
        ]);

        $review = Review::create([
            'user_id' => auth()->id(),
            'recipe_id' => $id,
            'review' => $data['review'],
            'rating' => $data['rating']
        ]);

        return redirect('/recipe/' . $id)->with('success', 'Review added successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $recipe, string $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        abort_if($review->recipe_id != $recipe, 404);

        $data = $request->validate([
            'review' => 'required|string',
            'rating' => 'required|numeric|in:1,2,3,4,5'
        ]);

        $review->update([
            'review' => $data['review'],
            'rating' => $data['rating']
        ]);

        return redirect('/recipe/' . $recipe);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $recipeId, string $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        abort_if($review->recipe_id != $recipeId, 404);
        abort_if($review->user_id !== auth()->id(), 403);

        $review->delete();

        return redirect('/recipe/' . $recipeId)->with('success', 'Review deleted');
    }
}
