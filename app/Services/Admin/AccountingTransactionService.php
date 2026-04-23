<?php

namespace App\Services\Admin;

use App\Models\AccountTransaction;
use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\Income;
use Illuminate\Support\Facades\DB;

class AccountingTransactionService
{
    public function postExpense(Expense $expense): void
    {
        DB::transaction(function () use ($expense) {
            AccountTransaction::query()
                ->where('reference_type', 'expense')
                ->where('reference_id', $expense->id)
                ->delete();

            if (! $expense->status) {
                $this->recalculateBalance($expense->financial_account_id);
                return;
            }

            AccountTransaction::create([
                'financial_account_id' => $expense->financial_account_id,
                'account_head_id' => $expense->account_head_id,
                'transaction_date' => $expense->transaction_date ?: now()->toDateString(),
                'direction' => 'out',
                'amount' => $expense->amount,
                'description' => $expense->name,
                'reference_type' => 'expense',
                'reference_id' => $expense->id,
                'note' => $expense->note,
                'status' => 1,
            ]);

            $this->recalculateBalance($expense->financial_account_id);
        });
    }

    public function removeExpense(Expense $expense): void
    {
        AccountTransaction::query()
            ->where('reference_type', 'expense')
            ->where('reference_id', $expense->id)
            ->delete();

        $this->recalculateBalance($expense->financial_account_id);
    }

    public function postIncome(Income $income): void
    {
        DB::transaction(function () use ($income) {
            AccountTransaction::query()
                ->where('reference_type', 'income')
                ->where('reference_id', $income->id)
                ->delete();

            if (! $income->status) {
                $this->recalculateBalance($income->financial_account_id);
                return;
            }

            AccountTransaction::create([
                'financial_account_id' => $income->financial_account_id,
                'account_head_id' => $income->account_head_id,
                'transaction_date' => $income->received_at ?: now()->toDateString(),
                'direction' => 'in',
                'amount' => $income->amount,
                'description' => $income->name,
                'reference_type' => 'income',
                'reference_id' => $income->id,
                'note' => $income->note,
                'status' => 1,
            ]);

            $this->recalculateBalance($income->financial_account_id);
        });
    }

    public function removeIncome(Income $income): void
    {
        AccountTransaction::query()
            ->where('reference_type', 'income')
            ->where('reference_id', $income->id)
            ->delete();

        $this->recalculateBalance($income->financial_account_id);
    }

    public function postGeneralTransaction(array $payload): AccountTransaction
    {
        $transaction = AccountTransaction::create($payload);
        $this->recalculateBalance($payload['financial_account_id'] ?? null);

        return $transaction;
    }

    public function recalculateBalance(?int $financialAccountId): void
    {
        if (! $financialAccountId) {
            return;
        }

        $account = FinancialAccount::find($financialAccountId);

        if (! $account) {
            return;
        }

        $in = AccountTransaction::where('financial_account_id', $financialAccountId)
            ->where('status', 1)
            ->where('direction', 'in')
            ->sum('amount');
        $out = AccountTransaction::where('financial_account_id', $financialAccountId)
            ->where('status', 1)
            ->where('direction', 'out')
            ->sum('amount');

        $account->current_balance = (float) $account->opening_balance + (float) $in - (float) $out;
        $account->save();
    }
}
