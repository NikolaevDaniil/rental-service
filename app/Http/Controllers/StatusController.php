<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use App\Models\Purchase;
use App\Models\Rental;

class StatusController extends Controller
{
    public function purchaseStatus(int $id): JsonResponse
    {
        $user = request()->user();
        $purchase = Purchase::where('id', $id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

        if (!$purchase->unique_code) {
            $purchase->unique_code = (string) Str::uuid();
            $purchase->save();
        }

        return new JsonResponse([
            'purchase_id' => $purchase->id,
            'product_id'  => $purchase->product_id,
            'unique_code' => $purchase->unique_code,
            'created_at'  => $purchase->created_at,
        ]);
    }

    public function rentalStatus(int $id): JsonResponse
    {
        $user = request()->user();
        $rental = Rental::where('id', $id)
                        ->where('user_id', $user->id)
                        ->firstOrFail();

        if (!$rental->unique_code) {
            $rental->unique_code = (string) Str::uuid();
            $rental->save();
        }

        return new JsonResponse([
            'rental_id'   => $rental->id,
            'product_id'  => $rental->product_id,
            'unique_code' => $rental->unique_code,
            'rented_at'   => $rental->rented_at,
            'expires_at'  => $rental->expires_at,
        ]);
    }
}
