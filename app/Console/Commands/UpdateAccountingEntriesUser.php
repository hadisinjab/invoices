<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AccountingEntry;
use App\Models\Invoice;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class UpdateAccountingEntriesUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounting:update-user-entries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تحديث القيود المحاسبية لربطها بالمستخدمين الصحيحين';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('بدء تحديث القيود المحاسبية...');

        // تحديث القيود المحاسبية للفواتير
        $this->updateInvoiceEntries();

        // تحديث القيود المحاسبية للمصاريف
        $this->updateExpenseEntries();

        $this->info('تم تحديث القيود المحاسبية بنجاح!');
    }

    /**
     * تحديث القيود المحاسبية للفواتير
     */
    private function updateInvoiceEntries()
    {
        $this->info('تحديث القيود المحاسبية للفواتير...');

        $entries = AccountingEntry::whereNull('user_id')
            ->where('sourceable_type', Invoice::class)
            ->get();

        foreach ($entries as $entry) {
            $invoice = $entry->sourceable;
            if ($invoice && $invoice->user_id) {
                $entry->update(['user_id' => $invoice->user_id]);
                $this->line("تم تحديث القيد {$entry->id} للمستخدم {$invoice->user_id}");
            }
        }
    }

    /**
     * تحديث القيود المحاسبية للمصاريف
     */
    private function updateExpenseEntries()
    {
        $this->info('تحديث القيود المحاسبية للمصاريف...');

        $entries = AccountingEntry::whereNull('user_id')
            ->where('sourceable_type', Expense::class)
            ->get();

        foreach ($entries as $entry) {
            $expense = $entry->sourceable;
            if ($expense && $expense->user_id) {
                $entry->update(['user_id' => $expense->user_id]);
                $this->line("تم تحديث القيد {$entry->id} للمستخدم {$expense->user_id}");
            }
        }
    }
}
