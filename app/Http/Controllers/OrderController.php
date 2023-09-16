<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Mail\LowStockMail;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Place order
     * @param OrderRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function placeOrder(OrderRequest $request): JsonResponse
    {
        $orderProducts = $request->input('products');

        try {
            DB::beginTransaction();
            foreach ($orderProducts as $orderProduct) {
                $productId = (int)$orderProduct["product_id"];
                $qty = (int)$orderProduct["quantity"];
                $product = Product::findOrFail($productId);

                if (!$this->checkStock($product, $qty)) {
                    DB::rollBack();
                    return response()->json(['error' => 'Not sufficient ingredients']);
                }

                $order = Order::create();
                $order->items()->attach($productId, ['qty' => $qty]);

                foreach ($product->ingredients as $ingredient) {
                    $recipeAmount = $ingredient->pivot->recipe_amount;
                    $requiredStock = $qty * $recipeAmount;
                    $ingredient->available_stock -= $requiredStock;

                    if (!$ingredient->email_sent && ($ingredient->available_stock / $ingredient->stock < 0.5)) {

                        $this->sendLowStockNotification($ingredient);
                    }

                    $ingredient->save();
                }
            }

            DB::commit();

            return response()->json(['success' => 'Order placed successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while placing the order'], 500);
        }
    }

    /**
     * Check Stock and returns true if there is sufficient ingredients and false if not
     * @param Product $product
     * @param int $qty
     * @return bool
     */
    protected function checkStock(Product $product, int $qty): bool
    {
        foreach ($product->ingredients as $ingredient) {
            if ($ingredient->available_stock < ($qty * $ingredient->pivot->recipe_amount)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Send an email on low stock ingredient
     * @param Ingredient $ingredient
     */
    protected function sendLowStockNotification(Ingredient $ingredient): void
    {
        try {
            \Mail::to('foodics.info@foodics.com')->send(new LowStockMail($ingredient->name));
            $ingredient->update(['email_sent' => 1]);
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }
    }

}
