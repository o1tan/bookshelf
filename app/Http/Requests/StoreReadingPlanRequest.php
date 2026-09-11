<?php

namespace App\Http\Requests;

use App\Models\ReadingPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReadingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id' => [
                'required',
                'integer',
                'exists:books,id',
                Rule::unique('reading_plans')
                    ->where('user_id', $this->user()->id),
            ],
            'deadline' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'status' => [
                'required',
                Rule::in([
                    ReadingPlan::STATUS_NOT_STARTED,
                    ReadingPlan::STATUS_READING,
                    ReadingPlan::STATUS_COMPLETED,
                ]),
            ],
            'reminder_at' => [
                'nullable',
                'date',
                'after:now',
                'before_or_equal:deadline',
            ],
        ];
    }
}