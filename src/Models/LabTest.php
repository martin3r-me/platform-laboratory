<?php

namespace Platform\Laboratory\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\Uid\UuidV7;

/**
 * LabTest — ein Eintrag im Labor-Katalog (Anforderung/Analyt, LOINC). Referenziert von
 * erbrachten Leistungen (encounter Service) via morphMap-Alias 'lab_test'. Team-scoped.
 * Ergebnisse laufen perspektivisch als Observations (Core), nicht hier.
 *
 * @ai.description Laboranforderung/-analyt als Katalog-Referenz (LOINC).
 */
class LabTest extends Model
{
    use SoftDeletes;

    protected $table = 'lab_tests';

    protected $fillable = [
        'uuid', 'catalog_code', 'team_id', 'loinc_code', 'name', 'material', 'description',
        'status', 'position', 'created_by_user_id',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) UuidV7::generate();
            }
            if (empty($model->team_id) && auth()->check()) {
                $model->team_id = auth()->user()->currentTeam?->id;
            }
            if (empty($model->created_by_user_id) && auth()->check()) {
                $model->created_by_user_id = auth()->id();
            }
        });
    }

    public function scopeForTeam(Builder $query, int $teamId): Builder
    {
        return $query->where($this->getTable() . '.team_id', $teamId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /** Anzeigename: "Serum · γ-GT" oder nur Name. */
    public function label(): string
    {
        return $this->material ? trim($this->name . ' (' . $this->material . ')') : (string) $this->name;
    }
}
