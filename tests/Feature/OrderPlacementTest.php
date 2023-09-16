<?php

namespace Tests\Feature;

use App\Mail\LowStockMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Ingredient;
use Faker\Factory;

class OrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    public function testPlaceOrderWithSufficientStock(): void
    {
        // Create a product and ingredient with sufficient stock
        $faker = Factory::create();
        $product = Product::factory()->create(['name'=>$faker->name]);
        $ingredient = Ingredient::factory()->create([
            'name'=>$faker->name,
            'stock'=>100,
            'available_stock' => 100,
            'email_sent'=>0]);
        $product->ingredients()->attach($ingredient->id, ['recipe_amount' => 1]);
        $request = [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ];
        $response = $this->postJson('/placeOrder', $request);

        // Assert that the order was placed successfully
        $response->assertStatus(200)
            ->assertJson(['success' => 'Order placed successfully']);

        // Assert that the stock was updated
        $this->assertEquals(95, $ingredient->fresh()->available_stock);
    }

    public function testPlaceOrderWithInsufficientStock(): void
    {
        // Create a product and ingredient with insufficient stock
        $faker = Factory::create();
        $product = Product::factory()->create(['name'=>$faker->name]);
        $ingredient = Ingredient::factory()->create([
            'name'=>$faker->name,
            'stock'=>2,
            'available_stock' => 2,
            'email_sent'=>0]);
        $product->ingredients()->attach($ingredient->id, ['recipe_amount' => 1]);

        $request = [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ];
        $response = $this->postJson('/placeOrder', $request);

        // Assert that the order placement failed due to insufficient stock
        $response->assertStatus(200)
            ->assertJson(['error' => 'Not sufficient ingredients']);

        // Assert that the stock was not updated
        $this->assertEquals(2, $ingredient->fresh()->available_stock);
    }

    public function testLowStockNotification(): void
    {
        $faker = Factory::create();
        $product = Product::factory()->create(['name'=>$faker->name]);
        $ingredient = Ingredient::factory()->create([
            'name'=>$faker->name,
            'stock' => 100,
            'available_stock' => 40,
            'email_sent' => false,
        ]);

        $response = $this->post('/placeOrder', [
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertJson(['success' => 'Order placed successfully']); // Ensure the order was placed successfully

        // Check if the email notification was sent
        Mail::fake();
        Mail::send(new LowStockMail($ingredient->name));
        Mail::assertSent(LowStockMail::class);
        Mail::assertSent(LowStockMail::class, function ($mail) {
            $mail->build();
            $this->assertTrue($mail->hasTo('foodics.info@foodics.com'));
            return true;
        });

    }
}
