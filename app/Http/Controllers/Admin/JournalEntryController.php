<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountHead;
use App\Models\FinancialAccount;
use App\Models\JournalEntry;
use App\Services\Admin\JournalEntryService;
use Illuminate\Http\Request;
use Toastr;

class JournalEntryController extends Controller
{
    public function __construct(
        private JournalEntryService $journalEntryService
    ) {
    }

    public function index()
    {
        $data = JournalEntry::withCount('lines')->latest('entry_date')->paginate(20);
        return view('backEnd.accounts.journal_entry.index', compact('data'));
    }

    public function create()
    {
        $accountHeads = AccountHead::where('status', 1)->orderBy('name')->get();
        $financialAccounts = FinancialAccount::where('status', 1)->orderBy('name')->get();
        return view('backEnd.accounts.journal_entry.create', compact('accountHeads', 'financialAccounts'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'entry_date' => 'required|date',
            'reference_no' => 'required|string|max:255',
            'lines' => 'required|array|min:2',
        ]);

        $lines = collect($request->input('lines', []))
            ->filter(fn ($line) => ! empty($line['account_head_id']) && (((float) ($line['debit'] ?? 0)) > 0 || ((float) ($line['credit'] ?? 0)) > 0))
            ->values()
            ->all();

        $debit = collect($lines)->sum(fn ($line) => (float) ($line['debit'] ?? 0));
        $credit = collect($lines)->sum(fn ($line) => (float) ($line['credit'] ?? 0));

        if (count($lines) < 2 || round($debit, 2) !== round($credit, 2)) {
            Toastr::error('Debit and credit must be equal with at least 2 valid lines', 'Error');
            return redirect()->back()->withInput();
        }

        $this->journalEntryService->create([
            'entry_date' => $request->entry_date,
            'reference_no' => $request->reference_no,
            'description' => $request->description,
            'status' => 1,
        ], $lines);

        Toastr::success('Success', 'Journal entry created successfully');
        return redirect()->route('accounts.journal_entries.index');
    }
}
