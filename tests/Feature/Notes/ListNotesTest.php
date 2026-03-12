<?php

use App\Livewire\Notes\ListNotes;
use App\Models\Note;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = new_user();
});

it('shows the notes index page', function () {
    $this->actingAs($this->user);

    $this->get(route('notes.index'))
        ->assertOk()
        ->assertSee(__('Notes'));
});

it('renders the livewire component', function () {
    $this->actingAs($this->user);
    Livewire::test(ListNotes::class)->assertOk();
});

it('lists notes', function () {
    $this->actingAs($this->user);
    $notes = Note::factory()->count(2)->for($this->user, 'author')->create();
    $note = Note::factory()->create();

    Livewire::test(ListNotes::class)
        ->assertSee($notes[0]->title)
        ->assertSee($notes[1]->title)
        ->assertDontSee($note->title);
});

it('can create a note', function () {
    $this->actingAs($this->user);

    Livewire::test(ListNotes::class)
        ->set('createOpen', true)
        ->set('title', 'Mi nota')
        ->set('content', 'Contenido')
        ->call('create')
        ->assertSet('createOpen', false);

    expect(Note::count())->toBe(1);
    expect(Note::first()->title)->toBe('Mi nota');
});

it('validates create note fields', function () {
    $this->actingAs($this->user);

    Livewire::test(ListNotes::class)
        ->set('createOpen', true)
        ->set('title', '')
        ->set('content', '')
        ->call('create')
        ->assertHasErrors(['title' => 'required', 'content' => 'required']);
});

it('can open edit modal with note data', function () {
    $this->actingAs($this->user);
    $note = Note::factory()->for($this->user, 'author')->create([
        'title' => 'Viejo',
        'content' => 'Viejo contenido',
    ]);

    Livewire::test(ListNotes::class)
        ->call('openEdit', $note->id)
        ->assertSet('editOpen', true)
        ->assertSet('editingId', $note->id)
        ->assertSet('title', 'Viejo')
        ->assertSet('content', 'Viejo contenido');
});

it('can update a note', function () {
    $this->actingAs($this->user);
    $note = Note::factory()->for($this->user, 'author')->create();

    Livewire::test(ListNotes::class)
        ->call('openEdit', $note->id)
        ->set('title', 'Nuevo título')
        ->set('content', 'Nuevo contenido')
        ->call('update')
        ->assertSet('editOpen', false);

    $note->refresh();
    expect($note->title)->toBe('Nuevo título');
});

it('cannot open edit modal for a note from another user', function () {
    $this->actingAs($this->user);
    $foreignNote = Note::factory()->create();

    expect(fn () => Livewire::test(ListNotes::class)
        ->call('openEdit', $foreignNote->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

it('cannot update a note from another user', function () {
    $this->actingAs($this->user);
    $foreignNote = Note::factory()->create([
        'title' => 'Título ajeno',
        'content' => 'Contenido ajeno',
    ]);

    expect(fn () => Livewire::test(ListNotes::class)
        ->set('editingId', $foreignNote->id)
        ->set('title', 'Intento de cambio')
        ->set('content', 'Intento de contenido')
        ->call('update'))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    $foreignNote->refresh();
    expect($foreignNote->title)->toBe('Título ajeno');
});

it('can open delete modal', function () {
    $this->actingAs($this->user);
    $note = Note::factory()->for($this->user, 'author')->create();

    Livewire::test(ListNotes::class)
        ->call('openDelete', $note->id)
        ->assertSet('deleteOpen', true)
        ->assertSet('deletingId', $note->id);
});

it('can delete a note', function () {
    $this->actingAs($this->user);
    $note = Note::factory()->for($this->user, 'author')->create();

    Livewire::test(ListNotes::class)
        ->call('openDelete', $note->id)
        ->call('destroy')
        ->assertSet('deleteOpen', false);

    expect(Note::count())->toBe(0);
});

it('cannot delete a note from another user', function () {
    $this->actingAs($this->user);
    $foreignNote = Note::factory()->create();

    expect(fn () => Livewire::test(ListNotes::class)
        ->set('deletingId', $foreignNote->id)
        ->call('destroy'))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

    expect($foreignNote->fresh())->not->toBeNull();
});
