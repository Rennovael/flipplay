<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'unique:media,title'],
            'category' => ['required', 'string', Rule::in(['Anime', 'Manga', 'Drama', 'Light Novel'])],
            'genre' => ['required', 'string', 'max:120'],
            'synopsis' => ['required', 'string', 'max:5000'],
            'release_year' => ['required', 'integer', 'between:1900,2100'],
            'cover_url' => ['nullable', 'url', 'max:2048'],
        ];
    }
}
