<?php

namespace App\Http\Requests;

use App\Models\ReadingPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReadingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $readingPlan = $this->route('reading_plan');

        return $readingPlan !== null
            && $this->user()->id === $readingPlan->user_id;
    }

    public function rules(): array
    {
        return [
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