<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategories;
use App\Models\HrAdvance;
use App\Models\HrEmployee;
use App\Models\HrPayroll;
use App\Models\HrSalaryStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Toastr;

class HrPayrollController extends Controller
{
    private function buildPayrollNotes(?string $notes, float $autoDeduction): ?string
    {
        $lines = collect(preg_split('/\r\n|\r|\n/', (string) $notes))
            ->map(fn ($line) => trim((string) $line))
            ->filter(fn ($line) => $line !== '' && ! str_starts_with($line, 'Auto advance deduction applied:'))
            ->values();

        if ($autoDeduction > 0) {
            $lines->push('Auto advance deduction applied: ' . number_format($autoDeduction, 2, '.', ''));
        }

        return $lines->isNotEmpty() ? $lines->implode("\n") : null;
    }

    private function preparePayrollValues(Request $request): array
    {
        $payrollPeriod = sprintf('%04d-%02d-01', (int) $request->year, (int) $request->month);
        $salaryStructure = HrSalaryStructure::query()
            ->where('employee_id', $request->employee_id)
            ->where('status', 1)
            ->where(function ($query) use ($payrollPeriod) {
                $query->whereNull('effective_date')
                    ->orWhereDate('effective_date', '<=', $payrollPeriod);
            })
            ->latest('effective_date')
            ->latest('id')
            ->first();

        $basicSalary = (float) ($request->basic_salary ?: ($salaryStructure->basic_salary ?? 0));
        $bonus = (float) ($request->bonus ?: 0);

        $autoDeduction = HrAdvance::query()
            ->where('employee_id', $request->employee_id)
            ->whereIn('status', ['approved', 'running'])
            ->where(function ($query) use ($payrollPeriod) {
                $query->whereNull('deduction_start_date')
                    ->orWhereDate('deduction_start_date', '<=', $payrollPeriod);
            })
            ->sum('installment_amount');

        $deduction = (float) ($request->deduction ?: $autoDeduction);

        return [
            'basic_salary' => $basicSalary,
            'bonus' => $bonus,
            'deduction' => $deduction,
            'net_salary' => ($basicSalary + $bonus) - $deduction,
            'auto_deduction' => $autoDeduction,
            'salary_structure' => $salaryStructure,
        ];
    }

    private function syncPayrollExpense(HrPayroll $payroll): void
    {
        if (! Schema::hasTable('expenses')) {
            return;
        }

        $employee = $payroll->employee ?: HrEmployee::find($payroll->employee_id);

        if (! $employee) {
            return;
        }

        $supportsSourceReference = Schema::hasColumn('expenses', 'source_type') && Schema::hasColumn('expenses', 'source_id');
        $expenseName = 'Salary Payment - ' . $employee->name . ' - ' . str_pad((string) $payroll->month, 2, '0', STR_PAD_LEFT) . '/' . $payroll->year;
        $expenseQuery = Expense::query();

        if ($supportsSourceReference) {
            $expenseQuery->where('source_type', 'hr_payroll')
                ->where('source_id', $payroll->id);
        } else {
            $expenseQuery->where('name', $expenseName);
        }

        if ($payroll->status !== 'paid') {
            $expenseQuery->delete();
            return;
        }

        $categoryId = null;

        if (Schema::hasTable('expense_categories')) {
            $category = ExpenseCategories::firstOrCreate(
                ['slug' => 'salary'],
                ['name' => 'Salary', 'status' => 1]
            );

            $categoryId = $category->id;
        }

        $identifier = $supportsSourceReference
            ? [
                'source_type' => 'hr_payroll',
                'source_id' => $payroll->id,
            ]
            : [
                'name' => $expenseName,
            ];

        Expense::updateOrCreate(
            $identifier,
            [
                'name' => $expenseName,
                'expense_cat_id' => $categoryId,
                'amount' => $payroll->net_salary,
                'note' => $payroll->notes,
                'status' => 1,
            ]
        );
    }

    public function index(Request $request)
    {
        $data = HrPayroll::with('employee.department')->latest('year')->latest('month');

        if ($request->employee_id) {
            $data->where('employee_id', $request->employee_id);
        }

        if ($request->status) {
            $data->where('status', $request->status);
        }

        if ($request->month) {
            $data->where('month', $request->month);
        }

        if ($request->year) {
            $data->where('year', $request->year);
        }

        $data = $data->paginate(20)->withQueryString();
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();

        return view('backEnd.hr.payroll.index', compact('data', 'employees'));
    }

    public function create()
    {
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();

        return view('backEnd.hr.payroll.create', compact('employees'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $values = $this->preparePayrollValues($request);

        return response()->json([
            'basic_salary' => $values['basic_salary'],
            'bonus' => $values['bonus'],
            'deduction' => $values['deduction'],
            'net_salary' => $values['net_salary'],
            'auto_deduction' => $values['auto_deduction'],
            'salary_structure_found' => (bool) $values['salary_structure'],
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'required|exists:hr_employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'status' => 'required|string',
        ]);

        $payrollValues = $this->preparePayrollValues($request);

        $payroll = HrPayroll::create([
            'employee_id' => $request->employee_id,
            'month' => $request->month,
            'year' => $request->year,
            'payment_date' => $request->payment_date,
            'basic_salary' => $payrollValues['basic_salary'],
            'bonus' => $payrollValues['bonus'],
            'deduction' => $payrollValues['deduction'],
            'net_salary' => $payrollValues['net_salary'],
            'status' => $request->status,
            'notes' => $this->buildPayrollNotes($request->notes, (float) $payrollValues['auto_deduction']),
        ]);

        $this->syncPayrollExpense($payroll->loadMissing('employee'));

        Toastr::success('Success', 'Payroll entry created successfully');

        return redirect()->route('hr.payroll.index');
    }

    public function edit($id)
    {
        $edit_data = HrPayroll::findOrFail($id);
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();

        return view('backEnd.hr.payroll.edit', compact('edit_data', 'employees'));
    }

    public function payslip($id)
    {
        $payroll = HrPayroll::with('employee.department')->findOrFail($id);

        return view('backEnd.hr.payroll.payslip', compact('payroll'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:hr_payrolls,id',
            'employee_id' => 'required|exists:hr_employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'status' => 'required|string',
        ]);

        $payroll = HrPayroll::findOrFail($request->id);
        $payrollValues = $this->preparePayrollValues($request);

        $payroll->employee_id = $request->employee_id;
        $payroll->month = $request->month;
        $payroll->year = $request->year;
        $payroll->payment_date = $request->payment_date;
        $payroll->basic_salary = $payrollValues['basic_salary'];
        $payroll->bonus = $payrollValues['bonus'];
        $payroll->deduction = $payrollValues['deduction'];
        $payroll->net_salary = $payrollValues['net_salary'];
        $payroll->status = $request->status;
        $payroll->notes = $this->buildPayrollNotes($request->notes, (float) $payrollValues['auto_deduction']);
        $payroll->save();

        $this->syncPayrollExpense($payroll->loadMissing('employee'));

        Toastr::success('Success', 'Payroll entry updated successfully');

        return redirect()->route('hr.payroll.index');
    }

    public function destroy(Request $request)
    {
        $ids = array_filter((array) $request->input('payroll_ids', []));
        $supportsSourceReference = Schema::hasTable('expenses')
            && Schema::hasColumn('expenses', 'source_type')
            && Schema::hasColumn('expenses', 'source_id');

        if (! empty($ids)) {
            if ($supportsSourceReference) {
                Expense::where('source_type', 'hr_payroll')->whereIn('source_id', $ids)->delete();
            }
            HrPayroll::whereIn('id', $ids)->delete();
        } elseif ($request->hidden_id) {
            if ($supportsSourceReference) {
                Expense::where('source_type', 'hr_payroll')->where('source_id', $request->hidden_id)->delete();
            }
            HrPayroll::where('id', $request->hidden_id)->delete();
        }

        Toastr::success('Success', 'Payroll entry deleted successfully');

        return redirect()->back();
    }
}
