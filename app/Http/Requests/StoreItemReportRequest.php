<?php

namespace App\Http\Requests;

use App\Enums\CustodyType;
use App\Enums\ItemCategory;
use App\Enums\ItemType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemReportRequest extends FormRequest
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
            'type' => ['required', Rule::in(ItemType::values())],
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'category' => ['required', Rule::in(ItemCategory::values())],
            'campus_zone_id' => ['required', 'integer', 'exists:campus_zones,id'],
            'location_detail' => ['nullable', 'string', 'max:160'],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            'color' => ['nullable', 'string', 'max:40'],
            'brand' => ['nullable', 'string', 'max:60'],
            'description' => ['required', 'string', 'min:10', 'max:1500'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'hidden_traits' => ['required_if:type,found', 'nullable', 'string', 'min:5', 'max:500'],
            'custody_type' => ['required_if:type,found', 'nullable', Rule::in(CustodyType::values())],
            'custody_post_id' => ['required_if:custody_type,at_custody_post', 'nullable', 'integer', 'exists:custody_posts,id'],
        ];
    }
}
