<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HrDocument;
use App\Models\HrEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Toastr;

class HrDocumentController extends Controller
{
    private function storeDocumentFile(Request $request, ?string $existingPath = null): ?string
    {
        if (! $request->hasFile('document_file')) {
            return $request->input('file_path', $existingPath);
        }

        $file = $request->file('document_file');
        $directory = public_path('uploads/hr/documents');

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if ($existingPath && str_starts_with($existingPath, 'uploads/hr/documents/')) {
            $existingFullPath = public_path($existingPath);
            if (File::exists($existingFullPath)) {
                File::delete($existingFullPath);
            }
        }

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return 'uploads/hr/documents/' . $filename;
    }

    public function index(Request $request)
    {
        $data = HrDocument::with('employee.department')->latest();
        if ($request->employee_id) { $data->where('employee_id', $request->employee_id); }
        $data = $data->paginate(20)->withQueryString();
        $employees = HrEmployee::where('status', 1)->orderBy('name')->get();
        return view('backEnd.hr.document.index', compact('data', 'employees'));
    }

    public function create() { $employees = HrEmployee::where('status', 1)->orderBy('name')->get(); return view('backEnd.hr.document.create', compact('employees')); }
    public function edit($id) { $edit_data = HrDocument::findOrFail($id); $employees = HrEmployee::where('status', 1)->orderBy('name')->get(); return view('backEnd.hr.document.edit', compact('edit_data', 'employees')); }

    public function store(Request $request)
    {
        $this->validate($request, ['employee_id' => 'required|exists:hr_employees,id', 'title' => 'required|string|max:255', 'document_file' => 'nullable|file|max:5120']);
        HrDocument::create($request->only('employee_id', 'title', 'document_type', 'document_number', 'expiry_date', 'notes') + ['file_path' => $this->storeDocumentFile($request), 'status' => $request->status ? 1 : 0]);
        Toastr::success('Success', 'Document created successfully');
        return redirect()->route('hr.documents.index');
    }

    public function update(Request $request)
    {
        $this->validate($request, ['id' => 'required|exists:hr_documents,id', 'employee_id' => 'required|exists:hr_employees,id', 'title' => 'required|string|max:255', 'document_file' => 'nullable|file|max:5120']);
        $row = HrDocument::findOrFail($request->id);
        $row->fill($request->only('employee_id', 'title', 'document_type', 'document_number', 'expiry_date', 'notes'));
        $row->file_path = $this->storeDocumentFile($request, $row->file_path);
        $row->status = $request->status ? 1 : 0;
        $row->save();
        Toastr::success('Success', 'Document updated successfully');
        return redirect()->route('hr.documents.index');
    }

    public function inactive(Request $request) { HrDocument::where('id', $request->hidden_id)->update(['status' => 0]); Toastr::success('Success', 'Document inactive successfully'); return redirect()->back(); }
    public function active(Request $request) { HrDocument::where('id', $request->hidden_id)->update(['status' => 1]); Toastr::success('Success', 'Document active successfully'); return redirect()->back(); }
    public function destroy(Request $request)
    {
        $ids = array_filter((array) $request->input('document_ids', []));
        $documents = ! empty($ids)
            ? HrDocument::whereIn('id', $ids)->get()
            : HrDocument::where('id', $request->hidden_id)->get();

        foreach ($documents as $document) {
            if ($document->file_path && str_starts_with($document->file_path, 'uploads/hr/documents/')) {
                $fullPath = public_path($document->file_path);
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
            $document->delete();
        }

        Toastr::success('Success', 'Document deleted successfully');
        return redirect()->back();
    }
}
