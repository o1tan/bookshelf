<?php

namespace App\Http\Requests;

use App\Models\ReadingPlan;
use Carbon\Carbon;
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
                ]),
            ],
            'reminder_at' => [
                'nullable',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    if (! $this->filled('deadline')) {
                        return;
                    }

                    $reminderAt = Carbon::parse($value);
                    $deadlineEnd = Carbon::parse(
                        $this->input('deadline')
                    )->endOfDay();

                    if ($reminderAt->greaterThan($deadlineEnd)) {
                        $fail(
                            '通知日時は読了期限当日までに設定してください。'
                        );
                    }
                },
            ],
        ];
    }
}
