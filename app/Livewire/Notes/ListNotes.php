<?php

declare(strict_types=1);

namespace App\Livewire\Notes;

use App\Models\Note;
use Blockpc\Traits\PaginationTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

final class ListNotes extends Component
{
    use PaginationTrait;

    public bool $createOpen = false;

    public bool $editOpen = false;

    public bool $viewOpen = false;

    public ?int $editingId = null;

    public bool $deleteOpen = false;

    public ?int $deletingId = null;

    public string $title = '';

    public string $content = '';

    public function mount(): void
    {
        $this->paginate = 9;
    }

    #[Layout('layouts.app')]
    #[Title('Notas')]
    public function render(): View
    {
        return view('livewire.notes.list-notes');
    }

    #[Computed()]
    public function notes(): LengthAwarePaginator
    {
        return Note::query()
            ->forUser(auth()->id())
            ->search($this->search)
            ->latest()
            ->paginate($this->paginate);
    }

    public function create(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('notes')->where('author_id', auth()->id())],
            'content' => ['required', 'string'],
        ], [
            'title.unique' => __('You already have a note with this title. Please choose a different one.'),
        ], [
            'title' => __('Title'),
            'content' => __('Content'),
        ]);

        $data['author_id'] = auth()->id();

        Note::create($data);

        $this->resetModalsVariables();
    }

    public function openEdit(int $noteId): void
    {
        $note = Note::query()
            ->forUser(auth()->id())
            ->findOrFail($noteId);

        $this->editingId = $note->id;
        $this->title = $note->title;
        $this->content = $note->content;
        $this->editOpen = true;
    }

    public function update(): void
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique('notes')->where('author_id', auth()->id())->ignore($this->editingId)],
            'content' => ['required', 'string'],
        ], [
            'title.unique' => __('You already have a note with this title. Please choose a different one.'),
        ], [
            'title' => __('Title'),
            'content' => __('Content'),
        ]);

        $note = Note::query()
            ->forUser(auth()->id())
            ->findOrFail($this->editingId);
        $note->update($data);

        $this->resetModalsVariables();
    }

    public function openDelete(int $noteId): void
    {
        $note = Note::query()
            ->forUser(auth()->id())
            ->findOrFail($noteId);

        $this->deletingId = $note->id;
        $this->deleteOpen = true;
    }

    public function openNote(int $noteId): void
    {
        $note = Note::query()
            ->forUser(auth()->id())
            ->findOrFail($noteId);

        $this->title = $note->title;
        $this->content = $note->content;
        $this->viewOpen = true;
    }

    public function destroy(): void
    {
        Note::query()
            ->forUser(auth()->id())
            ->findOrFail($this->deletingId)
            ->delete();

        $this->reset(['deletingId']);
        $this->resetModalsVariables();
    }

    public function cancel(): void
    {
        $this->resetModalsVariables();
    }

    private function resetModalsVariables(): void
    {
        $this->reset(['title', 'content', 'editingId', 'deletingId', 'createOpen', 'editOpen', 'deleteOpen', 'viewOpen']);
    }
}
