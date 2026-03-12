<?php

declare(strict_types=1);

namespace App\Livewire\Notes;

use App\Models\Note;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;

final class CreateDashboardNote extends Component
{
    public string $title = '';

    public string $content = '';

    public function render(): View
    {
        return view('livewire.notes.create-dashboard-note');
    }

    public function create(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('notes')->where('author_id', auth()->id())],
            'content' => ['required', 'string'],
        ], [
            'title.required' => __('notes.errors.title.required'),
            'title.string' => __('notes.errors.title.string'),
            'title.max' => __('notes.errors.title.max'),
            'title.unique' => __('notes.errors.title.unique'),
            'content.required' => __('notes.errors.content.required'),
            'content.string' => __('notes.errors.content.string'),
        ], [
            'title' => 'título',
            'content' => 'contenido',
        ]);

        $data['author_id'] = auth()->id();

        Note::create($data);

        $this->reset();
        $this->modal('create-note')->close();

        $this->redirect(route('notes.index'), true);
    }

    public function cancel(): void
    {
        $this->reset();
        $this->clearValidation();
        $this->modal('create-note')->close();
    }
}
