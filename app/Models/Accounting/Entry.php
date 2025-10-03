<?php

namespace App\Models\Accounting;

use App\Enums\AccountingCategoryType;
use App\Traits\LogsActivityForModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class Entry extends Model
{
    use HasFactory;
    use LogsActivityForModels;

    protected $table = 'accounting_entries';

    protected $fillable = [
        'title',
        'description',
        'amount',
        'category_id',
        'transaction_date',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'metadata' => 'array',
    ];

    protected $appends = [
        'signed_amount',
    ];

    /**
     * Get the signed amount (negative for expenses, positive for income)
     */
    public function getSignedAmountAttribute(): float
    {
        return $this->category->type === AccountingCategoryType::EXPENSE
            ? -$this->amount
            : $this->amount;
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function scopeType(Builder $query, AccountingCategoryType|string|null $type): Builder
    {
        if (!$type) {
            return $query;
        }

        $typeValue = $type instanceof AccountingCategoryType ? $type->value : $type;

        return $query->where('category_id', function ($query) use ($typeValue) {
            $query->select('id')
                  ->from('accounting_categories')
                  ->where('type', $typeValue)
                  ->limit(1);
        });
    }


    public function scopeCategory(Builder $query, int|null $categoryId): Builder
    {
        if (!$categoryId) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope by date range
     */
    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->where('transaction_date', '>=', Carbon::parse($from));
        }

        if ($to) {
            $query->where('transaction_date', '<=', Carbon::parse($to));
        }
        
        return $query;
    }


    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for income entries
     */
    public function scopeIncome(Builder $query): Builder
    {
        return $query->where('category_id', function ($query) {
            $query->select('id')
                  ->from('accounting_categories')
                  ->where('type', AccountingCategoryType::INCOME->value)
                  ->limit(1);
        });
    }

    /**
     * Scope for expense entries
     */
    public function scopeExpense(Builder $query): Builder
    {
        return $query->where('category_id', function ($query) {
            $query->select('id')
                  ->from('accounting_categories')
                  ->where('type', AccountingCategoryType::EXPENSE->value)
                  ->limit(1);
        });
    }

    /**
     * Scope entries by current month
     */
    public function scopeCurrentMonth(Builder $query): Builder
    {
        return $query->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year);
    }

    /**
     * Scope entries by current year
     */
    public function scopeCurrentYear(Builder $query): Builder
    {
        return $query->whereYear('transaction_date', now()->year);
    }
}
