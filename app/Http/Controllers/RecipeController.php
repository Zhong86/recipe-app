<?php

namespace App\Http\Controllers;

use App\Models\ForkRecipe;
use App\Models\Recipe;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\CssSelector\Node\FunctionNode;

class RecipeController extends Controller
{
    public $validations = [
        'title'                  => 'required|string|max:255',
        'description'            => 'nullable|string',
        'cook_time'              => 'required|integer|min:1',
        'serving'                => 'required|integer|min:1',
        'category'               => 'required|in:breakfast,lunch,dinner,snack,dessert,drink',
        'image'                  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'ingredients'            => 'required|array|min:1',
        'ingredients.*.name'     => 'required|string',
        'ingredients.*.quantity' => 'required|numeric|min:0',
        'ingredients.*.unit'     => 'required|in:tsp,tbsp,cup,ml,l,g,kg,oz,lb,piece,slice,pinch,bunch',
        'steps'                  => 'required|array|min:1',
        'steps.*'                => 'required|string',
        'tags' => 'nullable|array',
        'tags.*' => 'nullable|string',
        'is_public' => 'required|in:true,false'
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //display the tags, image,
        $query = Recipe::with(['user', 'ingredients', 'tags'])
            ->where('is_public', true)
            ->withCount('usersLike')
            ->latest();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        }

        if ($request->filled('category')) {
            $search = $request->input('category');
            $query->where('category', $search);
        }

        if ($request->filled('tag')) {
            $tag = $request->input('tag');
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('name', $tag);
            });
        }

        $recipes = $query->paginate(12)->withQueryString();

        return view('recipes/index', compact('recipes'));
    }

    public function indexUser(Request $request) {
        $userId = auth()->id();
        $ownRecipe = Recipe::where('user_id', $userId)
            ->with(['user', 'ingredients', 'tags'])
            ->withCount('usersLike')
            ->select('recipes.*');
        $likedRecipe = Recipe::whereHas('usersLike',
            fn($q) => $q->where('user_id', $userId))
            ->with(['user', 'ingredients', 'tags'])
            ->withCount('usersLike')
            ->select('recipes.*');

        if ($request->filled('q')) {
            $search = $request->input('q');
            $ownRecipe->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
            $likedRecipe->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        }

        if ($request->filled('category')) {
            $search = $request->input('category');
            $ownRecipe->where('category', $search);
            $likedRecipe->where('category', $search);
        }

        if ($request->filled('tag')) {
            $tag = $request->input('tag');
            $ownRecipe->whereHas('tags', function ($q) use ($tag) {
                $q->where('name', $tag);
            });
            $likedRecipe->whereHas('tags', function ($q) use ($tag) {
                $q->where('name', $tag);
            });
        }

        $recipes = $likedRecipe->union($ownRecipe)
            ->latest()
            ->paginate(12)->withQueryString();

        return view('recipes/indexUser', compact('recipes'));
    }

    public function toggleLike(Recipe $recipe) {
        auth()->user()->likedRecipes()->toggle($recipe);
        $isLiked = auth()->user()->likedRecipes->contains($recipe);
        return response()->json([
            'is_liked' => $isLiked,
            'likes_count' => $recipe->usersLike()->count()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('recipes/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->validations);

        $imageUrl = '';
        $imageHash = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageHash = md5_file($file->getRealPath());

            $path = Storage::disk('s3')->putFile('recipes', $file);
            $imageUrl = Storage::disk('s3')->url($path);
        }

        DB::transaction(function() use ($data, $imageHash, $imageUrl) {
            $recipe = Recipe::create([
                'user_id' => auth()->id(),
                'title' => $data['title'],
                'description' => $data['description'],
                'cook_time' => $data['cook_time'],
                'serving' => $data['serving'],
                'category' => $data['category'],
                'image_url' =>  $imageUrl,
                'image_hash' => $imageHash ?? null,
                'is_public' => $data['is_public'] === 'true'
            ]);

            foreach($data['ingredients'] as $ingredient) {
                $recipe->ingredients()->create($ingredient);
            }

            foreach ($data['steps'] as $order => $instruction) {
                $recipe->steps()->create([
                    'order' => $order + 1,
                    'instruction' => $instruction
                ]);
            }

            if (!empty($data['tags'])) {
                $tagIds = collect($data['tags'])->map(function ($tagName) {
                    return Tag::firstOrCreate(['name' => $tagName])->id;
                });
                $recipe->tags()->sync($tagIds);
            }
        });

        return redirect()->route('recipes/user')
            ->with('success', 'Recipe created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        if (!$recipe->is_public && $recipe->user_id !== auth()->id()) abort(403);

        $recipe->load(['user', 'ingredients', 'steps', 'reviews.user', 'tags', 'forkedFrom.original.user']);

        return response()
            ->view('recipes/recipe', compact('recipe'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $recipe = Recipe::with(['ingredients', 'steps', 'tags'])
            ->findOrFail($id);
        if($recipe->user_id !== auth()->id()) abort(403);

        return response()
            ->view('recipes/create', compact('recipe'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $recipe = Recipe::findOrFail($id);

        if ($recipe->user_id !== auth()->id()) abort(403);

        $data = $request->validate($this->validations);

        if ($request->hasFile('image')) {
            if($recipe->image_url) {
                Storage::disk('s3')->delete(parse_url($recipe->image_url, PHP_URL_PATH));
            }

            $file = $request->file('image');
            $data['image_hash'] = md5_file($file->getRealPath());
            $data['image_url'] = Storage::disk('s3')->url(
                Storage::disk('s3')->putFile('recipes', $file)
            );
        }

        DB::transaction(function () use ($recipe, $data){
            $recipe->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'cook_time' => $data['cook_time'],
                'serving' => $data['serving'],
                'category' => $data['category'],
                'image_url' => $data['image_url'] ?? $recipe->image_url,
                'image_hash' => $data['image_hash'] ?? $recipe->image_hash,
                'is_public' => $data['is_public'] === 'true'
            ]);

            $recipe->ingredients()->delete();
            foreach ($data['ingredients'] as $ingredient) {
                $recipe->ingredients()->create($ingredient);

            }

            $recipe->steps()->delete();
            foreach ($data['steps'] as $order => $instruction) {
                $recipe->steps()->create([
                    'order' => $order + 1,
                    'instruction' => $instruction
                ]);
            }

            if (isset($data['tags'])) {
                $tagIds = collect($data['tags'])->map(function ($tagName) {
                    return Tag::firstOrCreate(['name' => $tagName])->id;
                });
                $recipe->tags()->sync($tagIds);
            } else {
                $recipe->tags()->sync([]);
            }
        });

        return redirect(url('/recipe/'.$recipe->id));
    }

    public function showFork(string $id) {
        $recipe = Recipe::with(['ingredients', 'steps', 'tags'])->findOrFail($id);

        return response()
            ->view('recipes/create', compact('recipe'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function storeFork(Request $request, string $id) {
        $original = Recipe::findOrFail($id);
        $data = $request->validate($this->validations);

        $imageUrl  = $original->image_url;
        $imageHash = null;

        if(!$request->hasFile('image') && $original->image_hash) {
            return back()
                ->withInput()
                ->withErrors(['image' => 'Please use your own image for this forked recipe']);
        }

        if($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadedHash = md5_file($file->getRealPath());

            if ($uploadedHash === $original->image_hash) {
                return back()
                    ->withInput()
                    ->withErrors(['image' => 'Please use your own image for this forked recipe']);
            }
            $imageHash = $uploadedHash;
            $imageUrl  = Storage::disk('s3')->url(
                Storage::disk('s3')->putFile('recipes', $file)
            );
        }

        //check if steps are all the same
        $originalSteps = $original->steps->sortBy('order')->pluck('instruction')->toArray();
        $submittedSteps = array_values($data['steps']);

        if ($originalSteps === $submittedSteps) {
            return back()
                ->withInput()
                ->withErrors(['steps' => 'Your forked recipe must have at least one step changed from the original.']);
        }

        DB::transaction(function() use ($data, $imageHash, $imageUrl, $original) {
            $forkedRecipe = Recipe::create([
                'user_id'     => auth()->id(),
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'cook_time'   => $data['cook_time'],
                'serving'     => $data['serving'],
                'category'    => $data['category'],
                'image_url'   => $imageUrl,
                'image_hash' => $imageHash
            ]);

            foreach ($data['ingredients'] as $ingredient) {
                $forkedRecipe->ingredients()->create($ingredient);
            }
            foreach ($data['steps'] as $order => $instruction) {
                $forkedRecipe->steps()->create([
                    'order'       => $order + 1,
                    'instruction' => $instruction,
                ]);
            }

            if (!empty($data['tags'])) {
                $tagIds = collect($data['tags'])->map(fn($name) => Tag::firstOrCreate(['name' => $name])->id);
                $forkedRecipe->tags()->sync($tagIds);
            }

            // Record the fork relationship
            ForkRecipe::create([
                'original_recipe_id'        => $original->id,
                'forked_recipe_id' => $forkedRecipe->id,
            ]);
        });

        return redirect()->route('recipes/user')->with('success', 'Recipe forked successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $recipe = Recipe::findOrFail($id);

        if ($recipe->user_id !== auth()->id()) abort(403);

        if ($recipe->image_url) {
            Storage::disk('s3')->delete(parse_url($recipe->image_url, PHP_URL_PATH));
        }

        $recipe->delete();

        return redirect()->route('recipes/user')
            ->with('success', 'Recipe deleted');
    }
}
