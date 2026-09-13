<?php

namespace Platform\Laboratory\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Platform\Laboratory\Models\LabTest;

/**
 * Labor-Katalog — Verwaltung (Liste + Anlegen/Bearbeiten/Löschen).
 */
class Index extends Component
{
    public bool $showModal = false;
    public ?int $editingId = null;
    public array $form = ['loinc_code' => '', 'name' => '', 'material' => '', 'description' => '', 'position' => 0, 'status' => 'active'];
    public string $search = '';

    protected function teamId(): int
    {
        return (int) Auth::user()->currentTeam->id;
    }

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->form = ['loinc_code' => '', 'name' => '', 'material' => '', 'description' => '', 'position' => 0, 'status' => 'active'];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $t = LabTest::query()->forTeam($this->teamId())->findOrFail($id);
        $this->editingId = $t->id;
        $this->form = [
            'loinc_code'  => $t->loinc_code ?? '',
            'name'        => $t->name,
            'material'    => $t->material ?? '',
            'description' => $t->description ?? '',
            'position'    => (int) $t->position,
            'status'      => $t->status ?? 'active',
        ];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'form.loinc_code'  => ['nullable', 'string', 'max:32'],
            'form.name'        => ['required', 'string', 'max:255'],
            'form.material'    => ['nullable', 'string', 'max:255'],
            'form.description' => ['nullable', 'string'],
            'form.position'    => ['nullable', 'integer'],
            'form.status'      => ['required', 'string', 'in:active,archived'],
        ])['form'];

        $payload = [
            'team_id'     => $this->teamId(),
            'loinc_code'  => $data['loinc_code'] ?: null,
            'name'        => trim((string) $data['name']),
            'material'    => $data['material'] ?: null,
            'description' => $data['description'] ?: null,
            'position'    => (int) ($data['position'] ?? 0),
            'status'      => $data['status'],
        ];

        if ($this->editingId) {
            LabTest::query()->forTeam($this->teamId())->findOrFail($this->editingId)->update($payload);
        } else {
            LabTest::create($payload);
        }

        $this->showModal = false;
        $this->dispatch('toast', message: 'Laboranalyt gespeichert.', type: 'success');
    }

    public function delete(int $id): void
    {
        LabTest::query()->forTeam($this->teamId())->findOrFail($id)->delete();
    }

    public function render()
    {
        $items = LabTest::query()->forTeam($this->teamId())
            ->when($this->search !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('loinc_code', 'like', '%' . $this->search . '%')
                ->orWhere('material', 'like', '%' . $this->search . '%')))
            ->orderBy('position')->orderBy('name')->get();

        return view('laboratory::livewire.index', ['items' => $items])
            ->layout('platform::layouts.app');
    }
}
