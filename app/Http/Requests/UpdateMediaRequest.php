<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends FormRequest
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
        $media = $this->route('media');
        $mediaId = $media instanceof Media ? $media->id : $media;

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('media', 'title')->ignore($mediaId)],
            'category' => ['sometimes', 'required', 'string', Rule::in(['Anime', 'Manga', 'Drama', 'Light Novel'])],
            'genre' => ['sometimes', 'required', 'string', 'max:120'],
            'synopsis' => ['sometimes', 'required', 'string', 'max:5000'],
            'release_year' => ['sometimes', 'required', 'integer', 'between:1900,2100'],
            'cover_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
        ];
    }
}
