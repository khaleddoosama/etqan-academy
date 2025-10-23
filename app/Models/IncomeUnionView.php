<?php

namespace App\Models;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeUnionView extends Model
{
    protected $table = 'income_union_view';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'coupon_id',
        'category_id',
        'title',
        'source',
        'amount',
        'type',
        'category',
        'details',
        'paid_at',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'category_id' => 'integer',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function scopeCourseIncome($query)
    {
        return $query->where('category', 'like', 'course%');
    }

    public function scopeAccountingIncome($query)
    {
        return $query->where('type', '!=', 'manual');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('paid_at', [$startDate, $endDate]);
        }

        if ($startDate) {
            $query->where('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('paid_at', '<=', $endDate);
        }

        return $query;
    }

    public function scopeByAmountRange($query, $minAmount, $maxAmount = null)
    {
        $query->where('amount', '>=', $minAmount);

        if ($maxAmount) {
            $query->where('amount', '<=', $maxAmount);
        }

        return $query;
    }

    public function scopeByType($query, ?string $type)
    {
        if (!$type) {
            return $query;
        }

        if (in_array($type, ['auto', 'manual'], true)) {
            return $query->where('type', $type);
        }

        if ($type === 'expense') {
            return $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public function scopeByCategoryId($query, ?int $categoryId)
    {
        if (!$categoryId) {
            return $query;
        }

        return $query->where(function ($innerQuery) use ($categoryId) {
            $innerQuery->whereNull('category_id')
                ->orWhere('category_id', $categoryId);
        });
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($innerQuery) use ($term) {
            $likeTerm = '%' . $term . '%';
            $innerQuery
                ->where('title', 'like', $likeTerm)
                ->orWhere('details', 'like', $likeTerm)
                ->orWhere('category', 'like', $likeTerm)
                ->orWhere('source', 'like', $likeTerm)
                ->orWhere('type', 'like', $likeTerm)
                ->orWhereRaw('CAST(amount AS CHAR) LIKE ?', [$likeTerm]);
        });
    }

    public function scopeAutoIncome($query)
    {
        return $query->where('type', 'auto');
    }

    public function scopeManualIncome($query)
    {
        return $query->where('type', 'manual');
    }

    public function scopeBooksIncome($query)
    {
        return $query->where('category', 'books');
    }

    public function scopeInstallmentIncome($query)
    {
        return $query->where('category', 'course (installment)');
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 2);
    }

    public function getFormattedTypeAttribute(): string
    {
        return ucfirst($this->type);
    }

    public function getCoursesAttribute(): array
    {
        if ($this->isCourseIncome()) {
            return  explode(', ', $this->details);
        }

        return [];
    }


    public function getFormattedDateAttribute(): string
    {
        return $this->paid_at ? $this->paid_at->format('Y-m-d H:i:s') : '';
    }

    public function isCourseIncome(): bool
    {
        return $this->category === 'course';
    }

    public function isAccountingIncome(): bool
    {
        return $this->type === 'manual';
    }

    public function isAutoIncome(): bool
    {
        return $this->type === 'auto';
    }

    public function isManualIncome(): bool
    {
        return $this->type === 'manual';
    }

    public function isBooksIncome(): bool
    {
        return $this->category === 'books';
    }

    public function isInstallmentIncome(): bool
    {
        return $this->category === 'course (installment)';
    }
}
