<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateApiBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => [
                'required',
                'digits:13',
                Rule::unique('books', 'isbn')
                    ->ignore($this->route('book')),
            ],
            'published_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'genre_ids' => ['required', 'array', 'min:1'],
            'genre_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:genres,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'ユーザーIDは必須です。',
            'user_id.integer' => 'ユーザーIDは整数で指定してください。',
            'user_id.exists' => '指定されたユーザーが存在しません。',
            'title.required' => 'タイトルは必須です。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'author.required' => '著者名は必須です。',
            'author.max' => '著者名は255文字以内で入力してください。',
            'isbn.required' => 'ISBNは必須です。',
            'isbn.digits' => 'ISBNは13桁の数字で入力してください。',
            'isbn.unique' => 'このISBNはすでに登録されています。',
            'published_date.required' => '出版日は必須です。',
            'published_date.date' => '出版日は正しい日付で入力してください。',
            'description.max' => '説明は5000文字以内で入力してください。',
            'image_url.url' => '画像URLは正しいURL形式で入力してください。',
            'image_url.max' => '画像URLは2048文字以内で入力してください。',
            'genre_ids.required' => 'ジャンルを1つ以上指定してください。',
            'genre_ids.array' => 'ジャンルは配列で指定してください。',
            'genre_ids.min' => 'ジャンルを1つ以上指定してください。',
            'genre_ids.*.integer' => 'ジャンルIDは整数で指定してください。',
            'genre_ids.*.distinct' => '同じジャンルを重複して指定できません。',
            'genre_ids.*.exists' => '指定されたジャンルが存在しません。',
        ];
    }
}