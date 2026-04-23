<?php

namespace App\Services\Admin;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;

class JournalEntryService
{
    public function __construct(
        private AccountingTransactionService $accountingTransactionService
    ) {
    }

    public function create(array $header, array $lines): JournalEntry
    {
        return DB::transaction(function () use ($header, $lines) {
            $entry = JournalEntry::create($header);

            foreach ($lines as $line) {
                $lineModel = $entry->lines()->create([
                    'account_head_id' => $line['account_head_id'],
                    'financial_account_id' => $line['financial_account_id'] ?? null,
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'note' => $line['note'] ?? null,
                ]);

                if (($lineModel->debit ?? 0) > 0) {
                    $this->accountingTransactionService->postGeneralTransaction([
                        'financial_account_id' => $lineModel->financial_account_id,
                        'account_head_id' => $lineModel->account_head_id,
                        'transaction_date' => $entry->entry_date,
                        'direction' => 'in',
                        'amount' => $lineModel->debit,
                        'description' => $entry->reference_no ?: 'Journal Entry',
                        'reference_type' => 'journal_entry',
                        'reference_id' => $entry->id,
                        'note' => $lineModel->note,
                        'status' => 1,
                    ]);
                }

                if (($lineModel->credit ?? 0) > 0) {
                    $this->accountingTransactionService->postGeneralTransaction([
                        'financial_account_id' => $lineModel->financial_account_id,
                        'account_head_id' => $lineModel->account_head_id,
                        'transaction_date' => $entry->entry_date,
                        'direction' => 'out',
                        'amount' => $lineModel->credit,
                        'description' => $entry->reference_no ?: 'Journal Entry',
                        'reference_type' => 'journal_entry',
                        'reference_id' => $entry->id,
                        'note' => $lineModel->note,
                        'status' => 1,
                    ]);
                }
            }

            return $entry;
        });
    }
}
