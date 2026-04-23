<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountHead;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Toastr;

class AccountHeadController extends Controller
{
    public function index()
    {
        $data = AccountHead::with('parent')->latest()->get();
        return view('backEnd.accounts.account_head.index', compact('data'));
    }

    public function create()
    {
        $parents = AccountHead::where('status', 1)->orderBy('name')->get();
        return view('backEnd.accounts.account_head.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required|string|max:255', 'type' => 'required|string']);
        AccountHead::create([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'code' => $request->code ?: strtoupper(Str::substr(Str::slug($request->name, ''), 0, 6)),
            'type' => $request->type,
            'note' => $request->note,
            'status' => $request->status ? 1 : 0,
        ]);
        Toastr::success('Success', 'Account head created successfully');
        return redirect()->route('accounts.heads.index');
    }

    public function edit($id)
    {
        $edit_data = AccountHead::findOrFail($id);
        $parents = AccountHead::where('status', 1)->where('id', '!=', $id)->orderBy('name')->get();
        return view('backEnd.accounts.account_head.edit', compact('edit_data', 'parents'));
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:account_heads,id', 'name' => 'required|string|max:255', 'type' => 'required|string']);
        $row = AccountHead::findOrFail($request->id);
        $row->fill([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'note' => $request->note,
        ]);
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Account head updated successfully');
        return redirect()->route('accounts.heads.index');
    }
}
