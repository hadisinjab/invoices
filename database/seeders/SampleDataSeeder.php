<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Expense;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // الحصول على المستخدم الأول
        $user = User::first();

        if (!$user) {
            $this->command->error('لا يوجد مستخدم في النظام. يرجى تشغيل DatabaseSeeder أولاً.');
            return;
        }

        // إنشاء عملاء تجريبيين
        $clients = [
            ['name' => 'أحمد محمد', 'phone' => '0501234567', 'email' => 'ahmed@example.com'],
            ['name' => 'فاطمة علي', 'phone' => '0502345678', 'email' => 'fatima@example.com'],
            ['name' => 'محمد حسن', 'phone' => '0503456789', 'email' => 'mohammed@example.com'],
        ];

        foreach ($clients as $clientData) {
            Client::firstOrCreate(
                ['email' => $clientData['email']],
                array_merge($clientData, ['user_id' => $user->id])
            );
        }

        // إنشاء منتجات تجريبية
        $products = [
            ['name' => 'لابتوب HP', 'sale_price' => 2500, 'purchase_price' => 2000],
            ['name' => 'طابعة كانون', 'sale_price' => 800, 'purchase_price' => 600],
            ['name' => 'هاتف آيفون', 'sale_price' => 3500, 'purchase_price' => 3000],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['name' => $productData['name']],
                array_merge($productData, ['user_id' => $user->id])
            );
        }

        // إنشاء فواتير تجريبية
        $clients = Client::where('user_id', $user->id)->get();
        $products = Product::where('user_id', $user->id)->get();

        if ($clients->count() > 0 && $products->count() > 0) {
            // فاتورة بيع مقبوضة
            $invoice1 = Invoice::create([
                'user_id' => $user->id,
                'client_id' => $clients->first()->id,
                'invoice_number' => 'INV-001',
                'date' => Carbon::now()->subDays(5),
                'type' => 'بيع',
                'status' => 'مقبوضة',
                'total' => 3300,
                'total_after_discount' => 3300,
                'currency' => 'SAR',
                'received_currency' => 'SAR',
                'exchange_rate' => 1,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice1->id,
                'product_id' => $products->first()->id,
                'quantity' => 1,
                'price' => 2500,
                'total' => 2500,
                'price_received' => 2500,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice1->id,
                'product_id' => $products->get(1)->id,
                'quantity' => 1,
                'price' => 800,
                'total' => 800,
                'price_received' => 800,
            ]);

            // فاتورة بيع غير مقبوضة
            $invoice2 = Invoice::create([
                'user_id' => $user->id,
                'client_id' => $clients->get(1)->id,
                'invoice_number' => 'INV-002',
                'date' => Carbon::now()->subDays(3),
                'type' => 'بيع',
                'status' => 'غير مقبوضة',
                'total' => 3500,
                'total_after_discount' => 3500,
                'currency' => 'SAR',
                'received_currency' => 'SAR',
                'exchange_rate' => 1,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice2->id,
                'product_id' => $products->get(2)->id,
                'quantity' => 1,
                'price' => 3500,
                'total' => 3500,
                'price_received' => 0,
            ]);

            // فاتورة شراء
            $invoice3 = Invoice::create([
                'user_id' => $user->id,
                'client_id' => $clients->get(2)->id,
                'invoice_number' => 'INV-003',
                'date' => Carbon::now()->subDays(1),
                'type' => 'شراء',
                'status' => 'مقبوضة',
                'total' => 2000,
                'total_after_discount' => 2000,
                'currency' => 'SAR',
                'received_currency' => 'SAR',
                'exchange_rate' => 1,
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice3->id,
                'product_id' => $products->first()->id,
                'quantity' => 1,
                'price' => 2000,
                'total' => 2000,
                'price_received' => 2000,
            ]);
        }

        // إنشاء مصاريف تجريبية
        $expenses = [
            ['name' => 'مصاريف مكتبية', 'amount' => 500, 'expense_date' => Carbon::now()->subDays(7)],
            ['name' => 'مصاريف نقل', 'amount' => 300, 'expense_date' => Carbon::now()->subDays(4)],
            ['name' => 'مصاريف صيانة', 'amount' => 800, 'expense_date' => Carbon::now()->subDays(2)],
        ];

        foreach ($expenses as $expenseData) {
            Expense::firstOrCreate(
                ['name' => $expenseData['name'], 'expense_date' => $expenseData['expense_date']],
                array_merge($expenseData, ['user_id' => $user->id])
            );
        }

        $this->command->info('تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->info('تم إنشاء ' . $clients->count() . ' عميل');
        $this->command->info('تم إنشاء ' . $products->count() . ' منتج');
        $this->command->info('تم إنشاء 3 فواتير');
        $this->command->info('تم إنشاء 3 مصاريف');
    }
}
