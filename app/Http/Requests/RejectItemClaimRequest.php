<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectItemClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'review_notes' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }
}
