<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\RentalData;

class RentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'duration' => ['required', 'integer', 'in:4,8,12,24'],
        ];
    }

    public function toDTO(): RentalData
    {
        $data = $this->validated();

        return new RentalData(
            product_id: (int) $data['product_id'],
            duration:   (int) $data['duration']
        );
    }
}
