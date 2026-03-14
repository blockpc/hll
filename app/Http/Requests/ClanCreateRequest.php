<?php

namespace App\Http\Requests;

use App\Models\Clan;
use Illuminate\Foundation\Http\FormRequest;

class ClanCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Clan::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'alias' => ['required', 'string', 'max:8', 'unique:clans,alias'],
            'name' => ['required', 'string', 'max:32'],
            'slug' => ['required', 'string', 'max:255', 'unique:clans,slug'],
            'description' => ['nullable', 'string'],
            'discord' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'alias.required' => 'The clan alias is required.',
            'alias.unique' => 'This clan alias is already taken.',
            'name.required' => 'The clan name is required.',
            'slug.required' => 'The clan slug is required.',
            'slug.unique' => 'This clan slug is already taken.',
            'image.image' => 'The file must be a valid image.',
            'image.max' => 'The image must not exceed 2MB.',
        ];
    }
}
