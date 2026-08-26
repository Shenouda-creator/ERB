<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\User;
use App\Models\Currency;
use App\Models\Unit;
use App\Models\Branch;
use App\Models\Account;
use App\Models\CostCenter;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Company & User
        $company = Company::firstOrCreate(
            ['name' => 'شركة المقاولات المصرية']
        );

        $user = User::where('email', 'admin@example.com')->first();
        if (!$user) {
            $user = new User();
            $user->name = 'Admin';
            $user->email = 'admin@example.com';
            $user->password = Hash::make('password123');
            $user->company_id = $company->id;
            $user->save();
        }
        
        // Log in the user to bypass global scope restrictions on company_id
        auth()->login($user);

        // 2. Currency & Unit
        $egp = Currency::firstOrCreate(['code' => 'EGP'], ['name' => 'Egyptian Pound', 'symbol' => 'E£', 'exchange_rate' => 1]);
        $usd = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 48]);
        $eur = Currency::firstOrCreate(['code' => 'EUR'], ['name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 52]);

        $unitKg = Unit::firstOrCreate(['name' => 'Kilogram', 'symbol' => 'Kg']);
        $unitTon = Unit::firstOrCreate(['name' => 'Ton', 'symbol' => 'Ton']);
        $unitPcs = Unit::firstOrCreate(['name' => 'Pieces', 'symbol' => 'Pcs']);
        $unitM = Unit::firstOrCreate(['name' => 'Meter', 'symbol' => 'M']);

        // 3. Branch
        $branch1 = Branch::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'الفرع الرئيسي - القاهرة'],
            ['address' => 'القاهرة']
        );
        $branch2 = Branch::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'فرع الإسكندرية'],
            ['address' => 'الإسكندرية']
        );
        $branch3 = Branch::firstOrCreate(
            ['company_id' => $company->id, 'name' => 'فرع العاصمة الإدارية'],
            ['address' => 'العاصمة الإدارية']
        );

        // 4. Account (Chart of Accounts)
        $cashAcc = Account::firstOrCreate([
            'company_id' => $company->id,
            'code' => '1000'
        ], [
            'name' => 'النقدية',
            'type' => 'asset',
            'is_active' => true
        ]);
        
        $bankAcc = Account::firstOrCreate([
            'company_id' => $company->id,
            'code' => '1001'
        ], [
            'name' => 'بنك مصر',
            'parent_id' => $cashAcc->id,
            'type' => 'asset',
            'is_active' => true
        ]);

        $customersAcc = Account::firstOrCreate([
            'company_id' => $company->id,
            'code' => '1002'
        ], [
            'name' => 'العملاء',
            'type' => 'asset',
            'is_active' => true
        ]);
        
        $suppliersAcc = Account::firstOrCreate([
            'company_id' => $company->id,
            'code' => '2000'
        ], [
            'name' => 'الموردون',
            'type' => 'liability',
            'is_active' => true
        ]);

        $revenueAcc = Account::firstOrCreate([
            'company_id' => $company->id,
            'code' => '4000'
        ], [
            'name' => 'إيرادات المبيعات',
            'type' => 'revenue',
            'is_active' => true
        ]);

        $expensesAcc = Account::firstOrCreate([
            'company_id' => $company->id,
            'code' => '5000'
        ], [
            'name' => 'مصروفات عمومية',
            'type' => 'expense',
            'is_active' => true
        ]);

        // 5. CostCenter
        $cc1 = CostCenter::firstOrCreate(['company_id' => $company->id, 'code' => 'CC-01'], ['name' => 'مشروع العاصمة الإدارية']);
        $cc2 = CostCenter::firstOrCreate(['company_id' => $company->id, 'code' => 'CC-02'], ['name' => 'مشروع العلمين']);
        $cc3 = CostCenter::firstOrCreate(['company_id' => $company->id, 'code' => 'CC-03'], ['name' => 'مشروع الجلالة']);
        $cc4 = CostCenter::firstOrCreate(['company_id' => $company->id, 'code' => 'CC-04'], ['name' => 'الإدارة العامة']);
        
        $costCenters = collect([$cc1, $cc2, $cc3, $cc4]);

        // 6. Warehouse
        $wh1 = Warehouse::firstOrCreate(['company_id' => $company->id, 'branch_id' => $branch1->id, 'name' => 'مخزن القاهرة الرئيسي'], ['location' => 'القاهرة']);
        $wh2 = Warehouse::firstOrCreate(['company_id' => $company->id, 'branch_id' => $branch2->id, 'name' => 'مخزن الإسكندرية'], ['location' => 'الإسكندرية']);
        $wh3 = Warehouse::firstOrCreate(['company_id' => $company->id, 'branch_id' => $branch3->id, 'name' => 'مخزن العاصمة'], ['location' => 'العاصمة الإدارية']);
        
        // 7. Item
        $items = [];
        $itemNames = ['أسمنت بورتلاندي', 'حديد تسليح 12مم', 'رمل', 'زلط', 'طوب أحمر', 'مواسير PVC', 'سيراميك أرضيات', 'كابلات كهرباء', 'خشب موسكي', 'أسمنت أبيض'];
        $units = [$unitTon, $unitTon, $unitTon, $unitTon, $unitPcs, $unitPcs, $unitM, $unitM, $unitM, $unitKg];
        
        foreach($itemNames as $i => $name) {
            $items[] = Item::firstOrCreate([
                'company_id' => $company->id,
                'code' => 'ITM-' . str_pad($i+1, 3, '0', STR_PAD_LEFT)
            ], [
                'name' => $name,
                'unit_id' => $units[$i]->id,
                'reorder_level' => rand(10, 50)
            ]);
        }

        // 8. Shift
        $shift1 = Shift::firstOrCreate(['company_id' => $company->id, 'name' => 'وردية صباحية'], ['start_time' => '08:00', 'end_time' => '16:00']);
        $shift2 = Shift::firstOrCreate(['company_id' => $company->id, 'name' => 'وردية مسائية'], ['start_time' => '16:00', 'end_time' => '00:00']);
        $shift3 = Shift::firstOrCreate(['company_id' => $company->id, 'name' => 'وردية ليلية'], ['start_time' => '00:00', 'end_time' => '08:00']);
        $shifts = [$shift1, $shift2, $shift3];

        // 9. Employee
        $employees = [];
        $employeeNames = ['أحمد محمود', 'محمد علي', 'محمود سعيد', 'خالد مصطفى', 'ياسر جلال', 'طارق حسن', 'أسامة كمال', 'حسن شحاتة'];
        foreach($employeeNames as $i => $name) {
            $employees[] = Employee::firstOrCreate([
                'company_id' => $company->id,
                'name' => $name
            ], [
                'shift_id' => $shifts[array_rand($shifts)]->id
            ]);
        }

        // 10. Attendance
        foreach ($employees as $employee) {
            for ($i = 0; $i < 2; $i++) {
                $date = Carbon::now()->subDays(rand(1, 15))->format('Y-m-d');
                Attendance::firstOrCreate([
                    'company_id' => $company->id,
                    'employee_id' => $employee->id,
                    'date' => $date
                ], [
                    'check_in' => '08:00:00',
                    'check_out' => '16:00:00'
                ]);
            }
        }

        // 11. LeaveRequest
        $statuses = ['pending', 'approved', 'rejected'];
        for ($i = 0; $i < 5; $i++) {
            LeaveRequest::firstOrCreate([
                'company_id' => $company->id,
                'employee_id' => $employees[array_rand($employees)]->id,
                'start_date' => Carbon::now()->addDays(rand(1, 5))->format('Y-m-d')
            ], [
                'end_date' => Carbon::now()->addDays(rand(6, 10))->format('Y-m-d'),
                'reason' => 'ظروف عائلية أو صحية',
                'status' => $statuses[array_rand($statuses)]
            ]);
        }

        // 12. Supplier
        $suppliers = [];
        $supplierNames = ['شركة عز الدخيلة', 'أسمنت السويس', 'الشركة العربية للمواسير', 'شركة الكابلات', 'مورد أدوات صحية'];
        foreach($supplierNames as $name) {
            $supplier = Supplier::firstOrCreate([
                'company_id' => $company->id,
                'name' => $name
            ], [
                'phone' => '012' . rand(1000000, 9999999),
                'email' => 'supplier' . rand(1,100) . '@example.com'
            ]);
            $suppliers[] = $supplier;
        }

        // 13. Customer
        $customers = [];
        $customerNames = ['وزارة الإسكان', 'شركة أوراسكوم', 'شركة طلعت مصطفى', 'المقاولون العرب', 'عميل أهالي'];
        foreach($customerNames as $name) {
            $customer = Customer::firstOrCreate([
                'company_id' => $company->id,
                'name' => $name
            ], [
                'phone' => '010' . rand(1000000, 9999999),
                'email' => 'customer' . rand(1,100) . '@example.com'
            ]);
            $customers[] = $customer;
        }

        // 14. JournalEntry with Lines
        for ($i = 0; $i < 10; $i++) {
            $amount = rand(1000, 50000);
            
            $je = JournalEntry::create([
                'company_id' => $company->id,
                'date' => Carbon::now()->subDays(rand(1, 30))->format('Y-m-d'),
                'reference' => 'JE-' . time() . '-' . $i,
                'description' => 'قيد يومية تجريبي رقم ' . ($i+1)
            ]);

            $type = rand(1, 4);
            $debitAccId = $cashAcc->id;
            $creditAccId = $cashAcc->id;
            $ccId = null;

            if ($type == 1) {
                // شراء من مورد أو دفع لمورد
                $supp = $suppliers[array_rand($suppliers)];
                $debitAccId = $supp->account_id ?? $suppliersAcc->id; // Sub-account generated via booted()
                $creditAccId = $cashAcc->id;
            } elseif ($type == 2) {
                // تحصيل من عميل
                $cust = $customers[array_rand($customers)];
                $debitAccId = $bankAcc->id;
                $creditAccId = $cust->account_id ?? $customersAcc->id;
            } elseif ($type == 3) {
                // مصروفات على مركز تكلفة
                $debitAccId = $expensesAcc->id;
                $creditAccId = $bankAcc->id;
                $ccId = $costCenters->random()->id;
            } else {
                // مبيعات
                $cust = $customers[array_rand($customers)];
                $debitAccId = $cust->account_id ?? $customersAcc->id;
                $creditAccId = $revenueAcc->id;
                $ccId = $costCenters->random()->id;
            }

            JournalEntryLine::create([
                'journal_entry_id' => $je->id,
                'account_id' => $debitAccId,
                'cost_center_id' => $ccId,
                'debit' => $amount,
                'credit' => 0
            ]);

            JournalEntryLine::create([
                'journal_entry_id' => $je->id,
                'account_id' => $creditAccId,
                'cost_center_id' => null,
                'debit' => 0,
                'credit' => $amount
            ]);
        }
    }
}
