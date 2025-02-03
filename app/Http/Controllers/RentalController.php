<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Requests\RentalRequest;
use App\Http\Requests\RentalExtendRequest;
use App\Models\Product;
use App\Models\Rental;

class RentalController extends Controller
{
    public function rent(RentalRequest $request): JsonResponse
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

            $now = Carbon::now();
            $expiresAt = $now->copy()->addHours($dto->duration);

            $rental = Rental::query()->create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
                'rented_at'  => $now,
                'expires_at' => $expiresAt,
                'unique_code'=> null,
            ]);

            DB::commit();

            return new JsonResponse([
                'message'   => 'Аренда успешна',
                'rental_id' => $rental->id,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return new JsonResponse(['error' => 'Ошибка при аренде'], 500);
        }
    }

    public function extend(RentalExtendRequest $request): JsonResponse
    {
        $dto = $request->toDTO();
        $user = $request->user();

        $rental = Rental::query()
                        ->where('id', $dto->rental_id)
                        ->where('user_id', $user->id)
                        ->firstOrFail();

        $currentDuration = $rental->rented_at->diffInHours($rental->expires_at);
        $newTotalDuration = $currentDuration + $dto->duration;
        if ($newTotalDuration > 24) {
            return new JsonResponse([
                'error' => 'Общая продолжительность аренды не может превышать 24 часа'
            ], 403);
        }

        $rental->expires_at = $rental->expires_at->addHours($dto->duration);
        $rental->save();

        return new JsonResponse([
            'message'   => 'Аренда продлена',
            'rental_id' => $rental->id
        ], 200);
    }
}
