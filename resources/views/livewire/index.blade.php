{{-- Labor-Katalog — Verwaltung (nx-Design-System). --}}
<x-ui-page>
    <x-slot name="navbar">
        <x-ui-page-navbar title="Labor" />
    </x-slot>

    <x-slot name="actionbar">
        <x-ui-page-actionbar :breadcrumbs="[['label' => 'Labor', 'icon' => 'beaker']]">
            <x-nx-button variant="primary" size="sm" wire:click="openCreate">
                @svg('heroicon-o-plus', 'w-4 h-4')
                <span>Neuer Analyt</span>
            </x-nx-button>
        </x-ui-page-actionbar>
    </x-slot>

    <x-ui-page-container width="contained" spacing="space-y-6">
        <x-nx-section icon="heroicon-o-beaker" title="Labor-Katalog"
                      description="Anforderungen/Analyten; am Termin als Leistung wählbar. Ergebnisse laufen als Observation." :hint="$items->count()">
            <x-slot name="action">
                <x-nx-input-text name="search" wire:model.live.debounce.300ms="search" placeholder="Suche …" />
            </x-slot>

            @if($items->isEmpty())
                <x-nx-card>
                    <x-nx-empty icon="heroicon-o-beaker">Noch keine Analyten. Lege den ersten über „Neuer Analyt" an.</x-nx-empty>
                </x-nx-card>
            @else
                <x-nx-card flush>
                    <x-nx-table>
                        <x-nx-table-header>
                            <x-nx-table-header-cell>LOINC</x-nx-table-header-cell>
                            <x-nx-table-header-cell>Analyt</x-nx-table-header-cell>
                            <x-nx-table-header-cell>Material</x-nx-table-header-cell>
                            <x-nx-table-header-cell>Status</x-nx-table-header-cell>
                            <x-nx-table-header-cell align="right"></x-nx-table-header-cell>
                        </x-nx-table-header>
                        <x-nx-table-body>
                            @foreach($items as $t)
                                <x-nx-table-row wire:key="lab-{{ $t->id }}">
                                    <x-nx-table-cell class="text-[color:var(--nx-muted)] tabular-nums">{{ $t->loinc_code ?: '—' }}</x-nx-table-cell>
                                    <x-nx-table-cell>
                                        {{ $t->name }}
                                        @if($t->description)<div class="text-xs text-[color:var(--nx-faint)]">{{ $t->description }}</div>@endif
                                    </x-nx-table-cell>
                                    <x-nx-table-cell class="text-[color:var(--nx-muted)]">{{ $t->material ?: '—' }}</x-nx-table-cell>
                                    <x-nx-table-cell>
                                        <x-nx-badge :variant="$t->status === 'active' ? 'success' : 'default'">{{ $t->status === 'active' ? 'aktiv' : 'archiviert' }}</x-nx-badge>
                                    </x-nx-table-cell>
                                    <x-nx-table-cell align="right">
                                        <div class="flex justify-end gap-2">
                                            <x-nx-button variant="ghost" size="xs" wire:click="openEdit({{ $t->id }})">
                                                @svg('heroicon-o-pencil-square', 'w-4 h-4')
                                            </x-nx-button>
                                            <x-nx-button variant="danger" size="xs" wire:click="delete({{ $t->id }})" wire:confirm="Analyt löschen?">
                                                @svg('heroicon-o-trash', 'w-4 h-4')
                                            </x-nx-button>
                                        </div>
                                    </x-nx-table-cell>
                                </x-nx-table-row>
                            @endforeach
                        </x-nx-table-body>
                    </x-nx-table>
                </x-nx-card>
            @endif
        </x-nx-section>
    </x-ui-page-container>

    {{-- Anlegen/Bearbeiten --}}
    <x-nx-modal wire:model="showModal" size="md">
        <x-slot name="header">{{ $editingId ? 'Analyt bearbeiten' : 'Neuer Analyt' }}</x-slot>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <x-nx-input-text name="form.loinc_code" label="LOINC (optional)" wire:model="form.loinc_code" placeholder="z.B. 2324-2" />
                <x-nx-input-text name="form.material" label="Material (optional)" wire:model="form.material" placeholder="z.B. Serum" />
            </div>
            <x-nx-input-text name="form.name" label="Analyt" wire:model="form.name" placeholder="z.B. γ-GT" />
            <x-nx-input-textarea name="form.description" label="Beschreibung (optional)" wire:model="form.description" rows="2" />
            <div class="grid grid-cols-2 gap-4">
                <x-nx-input-text name="form.position" type="number" label="Position" wire:model="form.position" />
                <x-nx-input-select name="form.status" label="Status" wire:model="form.status"
                                   :options="['active' => 'aktiv', 'archived' => 'archiviert']" />
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-nx-button variant="ghost" wire:click="$set('showModal', false)">Abbrechen</x-nx-button>
                <x-nx-button variant="primary" wire:click="save">Speichern</x-nx-button>
            </div>
        </x-slot>
    </x-nx-modal>
</x-ui-page>
