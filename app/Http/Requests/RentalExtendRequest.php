<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\DTO\RentalExtendData;

class RentalExtendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rental_id' => ['required', 'integer', 'exists:rentals,id'],
            'duration' => ['required', 'integer', 'in:4,8,12,24'],
        ];
    }

    public function toDTO(): RentalExtendData
    {
        $data = $this->validated();

        return new RentalExtendData(
            rental_id: (int) $data['rental_id'],
            duration:  (int) $data['duration']
        );
    }
}
