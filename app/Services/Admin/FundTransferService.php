<?php

namespace App\Services\Admin;

use App\Models\FundTransfer;
use Illuminate\Support\Facades\DB;

class FundTransferService
{
    public function __construct(
        private AccountingTransactionService $accountingTransactionService
    ) {
    }

    public function post(FundTransfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            $this->accountingTransactionService->postGeneralTransaction([
                'financial_account_id' => $transfer->from_financial_account_id,
                'account_head_id' => $transfer->account_head_id,
                'transaction_date' => $transfer->transfer_date,
                'direction' => 'out',
                'amount' => $transfer->amount,
                'description' => 'Fund Transfer Out',
                'reference_type' => 'fund_transfer',
                'reference_id' => $transfer->id,
                'note' => $transfer->note,
                'status' => 1,
            ]);

            $this->accountingTransactionService->postGeneralTransaction([
                'financial_account_id' => $transfer->to_financial_account_id,
                'account_head_id' => $transfer->account_head_id,
                'transaction_date' => $transfer->transfer_date,
                'direction' => 'in',
                'amount' => $transfer->amount,
                'description' => 'Fund Transfer In',
                'reference_type' => 'fund_transfer',
                'reference_id' => $transfer->id,
                'note' => $transfer->note,
                'status' => 1,
            ]);
        });
    }
}
