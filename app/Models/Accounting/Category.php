<?php

namespace App\Models\Accounting;

use App\Enums\AccountingCategoryType;
use App\Traits\LogsActivityForModels;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory;
    use LogsActivityForModels;

    protected $table = 'accounting_categories';

    protected $fillable = [
        'name',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'type' => AccountingCategoryType::class,
        'is_active' => 'boolean',
    ];


    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeType(Builder $query, AccountingCategoryType|string|null $type): Builder
    {
        if (!$type) {
            return $query;
        }

        $typeValue = $type instanceof AccountingCategoryType ? $type->value : $type;

        return $query->where('type', $typeValue);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class, 'category_id');
    }
}
