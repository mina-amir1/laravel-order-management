<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;

class IngredientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ingredients = [
            [
                'name' => 'beef',
                'stock' => 20,
                'available_stock' => 20,
                'email_sent' => 0
            ],
            [
                'name' => 'cheese',
                'stock' => 5,
                'available_stock' => 5,
                'email_sent' => 0
            ],
            [
                'name' => 'onion',
                'stock' => 1,
                'available_stock' => 1,
                'email_sent' => 0
            ]
        ];
        foreach ($ingredients as $ingredient) {
            Ingredient::create($ingredient);
        }
        /** @var Product $burger */
        $burger = Product::where('name', 'Burger')->first();
        if ($burger) {
            $burger->ingredients()->attach(1, ['recipe_amount' => 0.15]);
            $burger->ingredients()->attach(2, ['recipe_amount' => 0.03]);
            $burger->ingredients()->attach(3, ['recipe_amount' => 0.02]);
        }
    }
}
