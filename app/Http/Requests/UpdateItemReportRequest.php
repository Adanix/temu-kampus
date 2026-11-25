<?php

namespace App\Http\Requests;

use App\Enums\CustodyType;
use App\Enums\ItemCategory;
use App\Enums\ItemType;
use App\Models\ItemReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var ItemReport|null $item */
        $item = $this->route('item');

        return $this->user() !== null && $item !== null && $this->user()->can('update', $item);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var ItemReport $item */
        $item = $this->route('item');
        $isFound = $item->type === ItemType::Found;

        return [
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'category' => ['required', Rule::in(ItemCategory::values())],
            'campus_zone_id' => ['required', 'integer', 'exists:campus_zones,id'],
            'location_detail' => ['nullable', 'string', 'max:160'],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            'color' => ['nullable', 'string', 'max:40'],
            'brand' => ['nullable', 'string', 'max:60'],
            'description' => ['required', 'string', 'min:10', 'max:1500'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'hidden_traits' => [Rule::requiredIf($isFound), 'nullable', 'string', 'min:5', 'max:500'],
            'custody_type' => [Rule::requiredIf($isFound), 'nullable', Rule::in(CustodyType::values())],
            'custody_post_id' => [
                Rule::requiredIf(fn (): bool => $isFound && $this->input('custody_type') === CustodyType::AtCustodyPost->value),
                'nullable',
                'integer',
                'exists:custody_posts,id',
            ],
        ];
    }
}
