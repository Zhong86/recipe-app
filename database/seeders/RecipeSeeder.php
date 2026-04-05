<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Step;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name' => 'test',
            'email' => 'test@gmail.com',
        ]);

        $this->createClassicPancakes($user->id);
        $this->createGarlicPasta($user->id);
        $this->createChocolateBrownies($user->id);
    }

    // ─────────────────────────────────────────────
    // Recipe 1: Classic Buttermilk Pancakes
    // ─────────────────────────────────────────────
    private function createClassicPancakes(int $userId): void
    {
        $recipe = Recipe::create([
            'user_id'     => $userId,
            'title'       => 'Classic Buttermilk Pancakes',
            'description' => 'Fluffy, golden pancakes with a tender crumb. Perfect for a lazy weekend morning served with maple syrup and fresh berries.',
            'cook_time'   => 20,
            'serving'     => 4,
            'category'    => 'breakfast',
            'image_url'   => 'https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=800',
        ]);

        $ingredients = [
            ['name' => 'all-purpose flour',      'quantity' => 1.50, 'unit' => 'cup'],
            ['name' => 'buttermilk',             'quantity' => 1.25, 'unit' => 'cup'],
            ['name' => 'egg',                    'quantity' => 1,    'unit' => 'piece'],
            ['name' => 'unsalted butter, melted','quantity' => 2,    'unit' => 'tbsp'],
            ['name' => 'granulated sugar',       'quantity' => 2,    'unit' => 'tbsp'],
            ['name' => 'baking powder',          'quantity' => 1,    'unit' => 'tsp'],
            ['name' => 'baking soda',            'quantity' => 0.50, 'unit' => 'tsp'],
            ['name' => 'salt',                   'quantity' => 1,    'unit' => 'pinch'],
            ['name' => 'vanilla extract',        'quantity' => 1,    'unit' => 'tsp'],
        ];

        foreach ($ingredients as $ing) {
            Ingredient::create(array_merge($ing, ['recipe_id' => $recipe->id]));
        }

        $steps = [
            'Whisk together the flour, sugar, baking powder, baking soda, and salt in a large bowl.',
            'In a separate bowl, beat the egg, then mix in the buttermilk, melted butter, and vanilla extract.',
            'Pour the wet ingredients into the dry ingredients and stir gently until just combined — a few lumps are perfectly fine. Do not overmix.',
            'Heat a non-stick skillet or griddle over medium heat and lightly grease with butter or cooking spray.',
            'Pour about ¼ cup of batter per pancake onto the skillet. Cook until bubbles form on the surface and the edges look set, about 2–3 minutes.',
            'Flip and cook for another 1–2 minutes until golden brown. Serve immediately with maple syrup and berries.',
        ];

        foreach ($steps as $order => $instruction) {
            Step::create([
                'recipe_id'   => $recipe->id,
                'order'       => $order + 1,
                'instruction' => $instruction,
            ]);
        }

        $tags = ['breakfast', 'quick', 'vegetarian', 'comfort food', 'sweet'];
        $this->attachTags($recipe, $tags);
    }

    // ─────────────────────────────────────────────
    // Recipe 2: Garlic Butter Pasta (Aglio e Olio)
    // ─────────────────────────────────────────────
    private function createGarlicPasta(int $userId): void
    {
        $recipe = Recipe::create([
            'user_id'     => $userId,
            'title'       => 'Garlic Butter Pasta (Aglio e Olio)',
            'description' => 'A classic Italian pantry pasta ready in 20 minutes. Rich with olive oil, toasted garlic, chili flakes, and a shower of fresh parsley.',
            'cook_time'   => 20,
            'serving'     => 2,
            'category'    => 'dinner',
            'image_url'   => 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800',
        ]);

        $ingredients = [
            ['name' => 'spaghetti',              'quantity' => 200,  'unit' => 'g'],
            ['name' => 'garlic cloves, sliced',  'quantity' => 5,    'unit' => 'piece'],
            ['name' => 'extra virgin olive oil', 'quantity' => 4,    'unit' => 'tbsp'],
            ['name' => 'unsalted butter',        'quantity' => 30,   'unit' => 'g'],
            ['name' => 'red chili flakes',       'quantity' => 0.50, 'unit' => 'tsp'],
            ['name' => 'fresh parsley, chopped', 'quantity' => 1,    'unit' => 'bunch'],
            ['name' => 'parmesan cheese, grated','quantity' => 30,   'unit' => 'g'],
            ['name' => 'salt',                   'quantity' => 1,    'unit' => 'tsp'],
            ['name' => 'black pepper',           'quantity' => 1,    'unit' => 'pinch'],
        ];

        foreach ($ingredients as $ing) {
            Ingredient::create(array_merge($ing, ['recipe_id' => $recipe->id]));
        }

        $steps = [
            'Bring a large pot of generously salted water to a boil. Cook the spaghetti until al dente according to package instructions. Reserve 1 cup of pasta water before draining.',
            'While the pasta cooks, add olive oil and sliced garlic to a cold pan. Heat over medium-low heat, stirring frequently, until the garlic turns light golden — about 4 minutes. Watch closely so it doesn\'t burn.',
            'Add the chili flakes to the pan and stir for 30 seconds.',
            'Add the drained pasta directly to the pan along with the butter and a splash of pasta water. Toss vigorously over medium heat for 1–2 minutes until the sauce is glossy and coats the pasta.',
            'Remove from heat, toss in the fresh parsley, and season with salt and pepper.',
            'Plate and finish with grated parmesan. Serve immediately.',
        ];

        foreach ($steps as $order => $instruction) {
            Step::create([
                'recipe_id'   => $recipe->id,
                'order'       => $order + 1,
                'instruction' => $instruction,
            ]);
        }

        $tags = ['pasta', 'italian', 'quick', 'dinner', 'vegetarian'];
        $this->attachTags($recipe, $tags);
    }

    // ─────────────────────────────────────────────
    // Recipe 3: Fudgy Chocolate Brownies
    // ─────────────────────────────────────────────
    private function createChocolateBrownies(int $userId): void
    {
        $recipe = Recipe::create([
            'user_id'     => $userId,
            'title'       => 'Fudgy Chocolate Brownies',
            'description' => 'Dense, chewy, ultra-chocolatey brownies with a glossy crinkled top. Made in one bowl — no mixer needed.',
            'cook_time'   => 35,
            'serving'     => 9,
            'category'    => 'dessert',
            'image_url'   => 'https://images.unsplash.com/photo-1548365328-8c6db3220e4c?w=800',
        ]);

        $ingredients = [
            ['name' => 'dark chocolate (70%), chopped', 'quantity' => 170,  'unit' => 'g'],
            ['name' => 'unsalted butter',               'quantity' => 115,  'unit' => 'g'],
            ['name' => 'granulated sugar',              'quantity' => 200,  'unit' => 'g'],
            ['name' => 'brown sugar',                   'quantity' => 50,   'unit' => 'g'],
            ['name' => 'eggs',                          'quantity' => 2,    'unit' => 'piece'],
            ['name' => 'egg yolk',                      'quantity' => 1,    'unit' => 'piece'],
            ['name' => 'vanilla extract',               'quantity' => 1,    'unit' => 'tsp'],
            ['name' => 'all-purpose flour',             'quantity' => 65,   'unit' => 'g'],
            ['name' => 'cocoa powder',                  'quantity' => 2,    'unit' => 'tbsp'],
            ['name' => 'salt',                          'quantity' => 0.50, 'unit' => 'tsp'],
        ];

        foreach ($ingredients as $ing) {
            Ingredient::create(array_merge($ing, ['recipe_id' => $recipe->id]));
        }

        $steps = [
            'Preheat your oven to 175°C (350°F). Line a 20×20 cm (8×8 inch) baking pan with parchment paper, leaving some overhang on the sides.',
            'Melt the dark chocolate and butter together in a heatproof bowl set over a pot of simmering water (or microwave in 30-second bursts), stirring until smooth. Set aside to cool slightly.',
            'Whisk both sugars into the chocolate mixture until combined.',
            'Add the eggs, egg yolk, and vanilla extract. Whisk vigorously for about 1 minute — this step builds the shiny crinkled top.',
            'Fold in the flour, cocoa powder, and salt with a spatula until just no streaks of flour remain. Do not overmix.',
            'Pour the batter into the prepared pan and spread evenly.',
            'Bake for 22–25 minutes, until the edges are set but the centre still has a very slight wobble. A toothpick should come out with moist crumbs, not clean.',
            'Allow to cool completely in the pan before lifting out and slicing into 9 squares. Brownies firm up as they cool.',
        ];

        foreach ($steps as $order => $instruction) {
            Step::create([
                'recipe_id'   => $recipe->id,
                'order'       => $order + 1,
                'instruction' => $instruction,
            ]);
        }

        $tags = ['dessert', 'chocolate', 'baking', 'vegetarian', 'sweet'];
        $this->attachTags($recipe, $tags);
    }

    // ─────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────
    private function attachTags(Recipe $recipe, array $tagNames): void
    {
        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(['name' => $name]);
            $recipe->tags()->syncWithoutDetaching($tag->id);
        }
    }
}
