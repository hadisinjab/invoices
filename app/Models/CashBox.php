<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBox extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'initial_balance',
        'current_balance',
        'last_calculated_balance',
        'last_calculation_date',
        'notes'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'last_calculated_balance' => 'decimal:2',
        'last_calculation_date' => 'datetime'
    ];

    /**
     * علاقة الصندوق مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * حساب الرصيد من القيود المحاسبية
     */
    public function calculateBalanceFromEntries(?string $upToDate = null)
    {
        $query = AccountingEntry::query()
            ->where(function($q) {
                $q->where('debtor_type', 'صندوقي')
                    ->orWhere('creditor_type', 'صندوقي');
            })
            ->where('is_received', true)
            ->where('user_id', $this->user_id);

        if ($upToDate) {
            $query->where('entry_date', '<=', $upToDate);
        }

        $result = $query->get()->reduce(function ($balance, $entry) {
            if ($entry->debtor_type === 'صندوقي') {
                // الصندوق مدين = زيادة في الرصيد
                return $balance + $entry->amount;
            } elseif ($entry->creditor_type === 'صندوقي') {
                // الصندوق دائن = نقصان في الرصيد
                return $balance - $entry->amount;
            }
            return $balance;
        }, 0);

        return round($result, 2);
    }

    /**
     * تحديث الرصيد الحالي
     */
    public function updateCurrentBalance()
    {
        $newBalance = $this->calculateBalanceFromEntries();

        $this->update([
            'current_balance' => $newBalance,
            'last_calculated_balance' => $newBalance,
            'last_calculation_date' => now()
        ]);

        return $this;
    }

    /**
     * الحصول على حركات الصندوق
     */
    public function getMovements($startDate = null, $endDate = null)
    {
        $query = AccountingEntry::query()
            ->where(function($q) {
                $q->where('debtor_type', 'صندوقي')
                    ->orWhere('creditor_type', 'صندوقي');
            })
            ->where('is_received', true)
            ->where('user_id', $this->user_id)
            ->orderBy('entry_date', 'desc');

        if ($startDate) {
            $query->where('entry_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('entry_date', '<=', $endDate);
        }

        return $query->get()->map(function ($entry) {
            return [
                'date' => $entry->entry_date,
                'type' => $entry->operation_type,
                'description' => $entry->operation_description,
                'amount' => $entry->amount,
                'movement_type' => $entry->debtor_type === 'صندوقي' ? 'in' : 'out',
                'source_type' => class_basename($entry->sourceable_type),
                'source_id' => $entry->sourceable_id
            ];
        });
    }

    /**
     * الحصول على ملخص الحركات
     */
    public function getMovementsSummary($startDate = null, $endDate = null)
    {
        $query = AccountingEntry::query()
            ->where(function($q) {
                $q->where('debtor_type', 'صندوقي')
                    ->orWhere('creditor_type', 'صندوقي');
            })
            ->where('is_received', true)
            ->where('user_id', $this->user_id);

        if ($startDate) {
            $query->where('entry_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('entry_date', '<=', $endDate);
        }

        $inflow = (clone $query)
            ->where('debtor_type', 'صندوقي')
            ->sum('amount');

        $outflow = (clone $query)
            ->where('creditor_type', 'صندوقي')
            ->sum('amount');

        return [
            'total_in' => round($inflow, 2),
            'total_out' => round($outflow, 2),
            'net' => round($inflow - $outflow, 2)
        ];
    }

    /**
     * التحقق من صحة الرصيد
     */
    public function verifyBalance(): bool
    {
        $calculatedBalance = $this->calculateBalanceFromEntries();
        return abs($this->current_balance - $calculatedBalance) < 0.01;
    }

    /**
     * إعادة حساب الرصيد إذا كان غير صحيح
     */
    public function reconcileBalance()
    {
        if (!$this->verifyBalance()) {
            $this->updateCurrentBalance();
        }
        return $this;
    }

    /**
     * الحصول على الرصيد في تاريخ معين
     */
    public function getBalanceAtDate(string $date)
    {
        return $this->calculateBalanceFromEntries($date);
    }

    /**
     * تنسيق الرصيد مع العملة
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->current_balance, 2) . ' ' . config('app.currency', 'USD');
    }
}
