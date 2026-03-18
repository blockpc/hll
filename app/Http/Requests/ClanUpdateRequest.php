<?php

namespace App\Http\Requests;

use App\Models\Clan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClanUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $clan = $this->route('clan');

        if (! $clan instanceof Clan) {
            return false;
        }

        return $this->user()?->can('update', $clan) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clan = $this->route('clan');

        if (! $clan instanceof Clan) {
            return [
                'alias' => ['required', 'string', 'max:8', 'unique:clans,alias'],
                'name' => ['required', 'string', 'max:32'],
                'slug' => ['nullable', 'string', 'max:255', 'unique:clans,slug'],
                'description' => ['nullable', 'string'],
                'discord' => ['nullable', 'string', 'max:255'],
                'logo' => ['nullable', 'image', 'max:2048'],
                'image' => ['nullable', 'image', 'max:2048'],
            ];
        }

        return $this->rulesFor($clan);
    }

    /**
     * Get the validation rules for updating an existing clan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rulesFor(Clan $clan): array
    {
        return [
            'alias' => ['required', 'string', 'max:8', Rule::unique('clans', 'alias')->ignore($clan->id)],
            'name' => ['required', 'string', 'max:32'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('clans', 'slug')->ignore($clan->id)],
            'description' => ['nullable', 'string'],
            'discord' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
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
            'alias.string' => 'The clan alias must be a string.',
            'alias.max' => 'The clan alias must not exceed 8 characters.',
            'alias.unique' => 'This clan alias is already taken.',
            'name.required' => 'The clan name is required.',
            'name.string' => 'The clan name must be a string.',
            'name.max' => 'The clan name must not exceed 32 characters.',
            'slug.string' => 'The clan slug must be a string.',
            'slug.max' => 'The clan slug must not exceed 255 characters.',
            'slug.unique' => 'This clan slug is already taken.',
            'description.string' => 'The description must be a string.',
            'discord.string' => 'The Discord URL must be a string.',
            'logo.image' => 'The file must be a valid image.',
            'logo.max' => 'The image must not exceed 1MB.',
            'image.image' => 'The file must be a valid image.',
            'image.max' => 'The image must not exceed 1MB.',
            'discord.max' => 'The Discord URL must not exceed 255 characters.',
        ];
    }
}
