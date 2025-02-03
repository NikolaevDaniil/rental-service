<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Purchase;
use App\Models\Rental;

class HistoryController extends Controller
{
    public function index(): JsonResponse
    {
        $user = request()->user();
        $purchases = Purchase::with('product')->where('user_id', $user->id)->get();
        $rentals = Rental::with('product')->where('user_id', $user->id)->get();

        return new JsonResponse([
            'purchases' => $purchases,
            'rentals' => $rentals,
        ]);
    }
}
