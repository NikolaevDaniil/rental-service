<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\PurchaseData;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ];
    }

    public function toDTO(): PurchaseData
    {
        $data = $this->validated();

        return new PurchaseData(
            product_id: (int) $data['product_id']
        );
    }
}
