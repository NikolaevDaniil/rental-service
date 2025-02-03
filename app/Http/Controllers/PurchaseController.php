<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function purchase(PurchaseRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $user = $request->user();
        $product = Product::query()->findOrFail($dto->product_id);

        if ($user->balance < $product->price) {
            return new JsonResponse(['error' => 'Недостаточно средств'], 403);
        }

        DB::beginTransaction();
        try {
            $user->balance -= $product->price;
            $user->save();

            $purchase = Purchase::query()->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'unique_code' => null,
            ]);

            DB::commit();

            return new JsonResponse([
                'message' => 'Покупка успешна',
                'purchase_id' => $purchase->id,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return new JsonResponse(['error' => 'Ошибка при покупке'], 500);
        }
    }
}
